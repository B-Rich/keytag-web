<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vkharyt
 * Date: 1/6/12
 * Time: 5:31 PM
 * To change this template use File | Settings | File Templates.
 */
class ProgramsReport// extends CActiveRecord
{
    private $openedProgram = false;
    private $prevProgram = null;
    
    public $sessions;
    
    public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'BarcodeID' => 'Barcode',
			'LastName' => 'Last Name',
			
		);
	}

    public function report($currentProgram = null, $sessions = null, $schools = null, $sites = null, $fromDate = null, $toDate = null, $showHours = null, $perfectAttendance = null)
    {
        
        $filters = "";
        $differensHours = "";
        
        
        if(isset($currentProgram) and ($currentProgram != NULL))
        {
            $filters .= " WHERE programs.ID = :currentProgram ";
            $addfilters = " WHERE Program = :currentProgram ";
        } else {
            $filters .= " WHERE 1 ";
            $addfilters = "";
        }
        
        
        if(isset($sessions) and ($sessions != NULL))
        {   
            $firstSession = $sessions[0];
         
            $sessionsList = (string) implode(",", $sessions);
            $filters .= " AND sessions.ID in ($sessionsList) ";
            $addfilters .= " AND sessions.ID in ($sessionsList)";
        }
        
        $sessionsNumber = $sessions != NULL ? count($sessions) : "(select count(*) from sessions inner join programs on sessions.program=programs.id where programs.id=:currentProgram)";
        
        if(isset($schools) and ($schools != NULL))
        {   
            $schoolsList = (string) implode(",", $schools);
            $filters .= " AND sessions.SchoolSite in ($schoolsList) ";
        }
        
        if(isset($fromDate) and ($fromDate != NULL))
        {   
            $filters .= " AND programs.StartDate >= :fromDate ";
        }
        
        if(isset($toDate) and ($toDate != NULL))
        {   
            $filters .= " AND programs.EndDate <= :toDate ";
        }
        
        if(isset($sites) and ($sites != NULL))
        {   
            $sitesList = (string) implode(",", $sites);
            $filters .= " AND sessions.NonSchoolSite in ($sitesList) ";
        }
                
        if(isset($sites) and ($sites != NULL))
        {   
            $sitesList = (string) implode(",", $sites);
            $filters .= " AND sessions.NonSchoolSite in ($sitesList) ";
        }

        if(isset($showHours) and ((int)$showHours === 1))
        {   
            $differensHours = "TIMESTAMPDIFF(MINUTE, attendance.EnterDate, attendance.ExitDate) Hours,";//'TIME_FORMAT(TIMEDIFF(attendance.ExitDate, attendance.EnterDate), "%H:%i") Hours, ';
        }
        
        if((isset($perfectAttendance) and ((int)$perfectAttendance === 1)) and (isset($currentProgram) and ($currentProgram != NULL)))
        {   
            $perfectAttendees = "
                 INNER JOIN 
                 (select PersonsID, count(AttendanceNumber) allS from (
                 SELECT sessions.Id SessionId, persons.ID PersonsID, persons.barcodeid, COUNT(*) AttendanceNumber FROM  attendance LEFT JOIN sessions ON attendance.Session=sessions.ID LEFT JOIN 
                    programs ON 
                    sessions.Program = programs.ID LEFT JOIN persons ON attendance.Person=persons.ID "  
                    .$filters." GROUP BY persons.ID, sessions.ID) as ss group by  PersonsID having allS=".$sessionsNumber. 
                    ") AS b 
                 using (PersonsID)
            ";
        }
        
        if (Yii::app()->user->checkAccess('Local Admin')) {
       //     $filters .= " AND household.Location = :userLocation ";
        } elseif (Yii::app()->user->checkAccess('School Representative')) {
         //   $filters .= " AND schools.Location = :userLocation ";
        }
        
        
        $connection=Yii::app()->db;

        $strSQL= "
                
                SELECT *, COUNT(DISTINCT PersonsID) uniquePersons, COUNT(DISTINCT HouseholdID) uniqueHouseholds
               
                    FROM (
                        SELECT DISTINCT programs.Description Program, programs.StartDate ProgramStartDate, programs.EndDate ProgramEndDate, programs.Id ProgramId, sessions.Id SessionId, sessions.StartDate as SessionStart,
                          persontype.Id PersonTypeId, persontype.Name PersonType, personsubtype.Id PersonSubTypeId, personsubtype.Name PersonSubType,
                          household.ID HouseholdID, household.Name HouseholdName, household.Address as StreetAddress, household.ZIPPostal as Zipcode,".$differensHours.
                          "schools.Name as School,
                          state.Name as State, city.Name as City,
                          IF (sessions.NonSchoolSite is not NULL, sites.Name, ss.Name) as SiteName,
                          persons.ID PersonsID, BarcodeID, FirstName, LastName, Sex, DateOfBirth, persons.GradeLevel as Grade, MobilePhone as Cell, WorkPhone as Work, EmailAddress as Email
                        FROM ((((( attendance
                            LEFT JOIN sessions ON attendance.Session=sessions.ID)
                            LEFT JOIN programs ON sessions.Program = programs.ID)
                            INNER JOIN persons ON attendance.Person=persons.ID)
                            LEFT JOIN persontype ON persons.Type = persontype.ID)
                            LEFT JOIN personsubtype ON persons.Subtype = personsubtype.ID)
                            LEFT JOIN household ON persons.Household = household.ID
                            LEFT JOIN locations ON household.Location = locations.ID
                            LEFT JOIN country ON locations.Country = country.ID
                            LEFT JOIN state ON locations.State = state.ID
                            LEFT JOIN city ON locations.City = city.ID
                            LEFT JOIN schools ON persons.School = schools.ID
                            LEFT JOIN schools ss ON sessions.SchoolSite = ss.ID
                            LEFT JOIN sites ON sessions.NonSchoolSite = sites.ID
                            
                           
                         ".$filters."
        ORDER BY Zipcode ASC) AS a ".$perfectAttendees. 
        "GROUP BY ProgramId,PersonTypeId,PersonsID WITH ROLLUP"; 
        
        $newSql = "
                    select *, IF(a.SessID = b.commonSessID, a.SessID, 0) as SessionPresence 
                    from 
                    (
                    select distinct hh.ID as HouseholdID, hh.Name as HouseholdName, 
                    persons.ID as PersonID, persons.BarcodeID as BarcodeID, concat(persons.FirstName, ' ', persons.LastName) as StudentName, persontype.ID as PersontypeID, persontype.Name as Role, ROUND(DATEDIFF(CURDATE(), 
                    persons.DateOfBirth)/365) as Age, persons.DateOfBirth, persons.HomePhone as Telephone, persons.EmailAddress as Email, 'y' as PhotoWaiver, 
                    IF(attendance.Person is not NULL, 1, 0) as Present,".$differensHours."
                    programs.ID as ProgramId, programs.Description as PrDescr, programs.StartDate ProgramStartDate, programs.EndDate ProgramEndDate,
                    sessions.ID as SessID, sessions.Description as SessDescr, sessions.StartDate from

                                               persons 

                                                LEFT JOIN attendance ON persons.ID=attendance.Person

                                                LEFT JOIN sessions ON attendance.Session=sessions.ID
                                                LEFT JOIN programs ON sessions.Program = programs.ID
                                                LEFT JOIN persontype ON persons.Type = persontype.ID
                                                LEFT JOIN personsubtype ON persons.Subtype = personsubtype.ID
                                                LEFT JOIN household hh ON persons.Household = hh.ID
                                                LEFT JOIN locations ON hh.Location = locations.ID
                                                LEFT JOIN country ON locations.Country = country.ID
                                                LEFT JOIN state ON locations.State = state.ID
                                                LEFT JOIN city ON locations.City = city.ID
                                                LEFT JOIN schools ON persons.School = schools.ID
                                                LEFT JOIN schools ss ON sessions.SchoolSite = ss.ID
                                                LEFT JOIN sites ON sessions.NonSchoolSite = sites.ID

                    ".$filters."
                    order by PrDescr, SessDescr, HouseholdName, Role
                    ) as a
                    inner join
                    (
                    select ID as commonSessID, Description from sessions".$addfilters. 
                    ") as b
                    on a.SessId=b.commonSessId
                    group by StudentName, Description    
            ";
        
        
        $sessionsQuery = "
            select ID as sessionID, StartDate as sessionStartDate, Description from sessions ".$addfilters." ORDER BY StartDate";
        $commandSessions=$connection->createCommand($sessionsQuery);
        

        /*        if(isset($currentProgram))
        {
            $filters .= "WHERE Programs.ID = :currentProgram";
        }*/

        $command=$connection->createCommand($newSql);
        
        if(isset($currentProgram) and ($currentProgram != NULL))
        {
            $command->bindParam(":currentProgram", $currentProgram, PDO::PARAM_INT);
            $commandSessions->bindParam(":currentProgram", $currentProgram, PDO::PARAM_INT);
        }

        if(isset($fromDate) and ($fromDate != NULL))
        {
            $command->bindParam(":fromDate", $fromDate, PDO::PARAM_STR);
        }
        
        if(isset($toDate) and ($toDate != NULL))
        {
            $command->bindParam(":toDate", $toDate, PDO::PARAM_STR);
        }

        if (!Yii::app()->user->checkAccess('Super Admin')) {
           // $command->bindParam(":userLocation", Yii::app()->user->location, PDO::PARAM_INT);
        }
        
        $sessions=$commandSessions->queryAll(); 
        
        $rows=$command->queryAll(); 
        $sessions = array_merge(array(count($sessions)), $sessions);
        $new = array_merge($sessions, $rows);
        return $new; 
    }
    
    
}
