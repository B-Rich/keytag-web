<?php

/**
 * Google Docs
 *
 */
class GoogleDocs 
{
	private $_folder;
	private $_fileName;
	private $_fileContentType;

	public function __construct($fileName, $folder = GoogleCredentials::FOLDER, $fileContentType = 'text/csv')
	{
		$this->_fileName = $fileName;
		$this->_folder = $folder;
        $this->_fileContentType = $fileContentType;
	}

	public function export($outputCsv, $needRedirect = true)
	{
        Yii::import('ext.googlelogin');
        Yii::import('ext.xhttp');

        $login = new googlelogin(GoogleCredentials::EMAIL, GoogleCredentials::PASSWORD, googlelogin::documents);

        $data['headers'] = array(
            'Authorization' => $login->toAuthorizationheader(),
            'GData-Version' => '3.0',
            'Slug' => rawurlencode($this->_fileName),
            'Content-Type' => $this->_fileContentType ,
        );

        $data['post'] = $outputCsv;

        $uploadresponse = xhttp::fetch($this->_folder, $data);
        $message = 'Check file "'.$this->_fileName.'" in export folder.';

        if($uploadresponse['successful']) {
            $xmlFilesInfo = new SimpleXMLElement($uploadresponse['body']);

            foreach ($xmlFilesInfo->link as $link) {
                if( ($link['type'] == 'text/html') && ($link['rel'] == 'alternate') ) {
                	if( $needRedirect ) {
                		Yii::app()->getRequest()->redirect($link['href']);
                		break;
                	}
                	$message = 'Check file "<a href="'.$link['href'].'">'.$this->_fileName.'</a>" in export folder.';
                }
            }
        }

        return $message;
	}

	public function CSVExport(&$data, $needRedirect = true)
	{
		Yii::import('ext.ECSVExport');
        $csv = new ECSVExport( $data );
        $outputCsv = $csv->toCSV();

        $this->export($outputCsv, $needRedirect);
	}

}

?>
