<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

return array (
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'KeyTag System',
    
    // preloading 'log' component
    'preload'=>array('log', 'zend'),

    // autoloading model and component classes
    'import'=>array(
        'application.config.*',
        'application.models.*',
        'application.models.common.*',
        'application.models.media.*',
        'application.models.query.*',
        'application.models.domain.*',
        'application.models.domain.job.*',
        'application.models.domain.person.*',
        'application.models.domain.program.*',
        'application.models.domain.survey.*',
        'application.models.domain.synchronization.*',
        'application.models.processors.*',
        'application.models.query.household.*',
        'application.models.query.person.*',
        'application.models.query.program.*',
        'application.models.query.survey.*',
        'application.models.query.synchronization.*',
        'application.models.query.user.*',
        'application.components.*',
        'application.components.document.*',
        'application.components.document.entity.*',
        'application.components.document.loaders.*',
        'application.components.document.validators.*',
        'application.components.remoting.*',
        'application.components.survey.*',
        'application.components.synchronization.*',
        'application.controllers.*',
        'application.vendors.*',
        'application.vendors.PHPExcel.Classes.*',
        'application.extensions.es3.s3',
        'application.extensions.es3.eS3',
        'application.components.file.*',
        
    ),

    'modules'=>array(
        // uncomment the following to enable the Gii tool	
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'tinka2901',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            //'ipFilters'=>array('127.0.0.3','::1'),
        ),
    ),
    
    'defaultController'=>'Home',

    // application components
    'components'=>array(
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'loginUrl' => array('/home/login'),
            'authTimeout' => 3600*2
        ),
        'authManager'=>array(
            'class'=>'CPhpAuthManager',
        ),
        's3' => array(
            'class'=>'application.extensions.es3.es3',
            'bucket' => 'tinkatest',
            'uploadPath' => 'video/',
            'aKey'=>'XXXXXXXXXXXXXXX', 
            'sKey'=>'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
         ),
            
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=keytag_system',
            'emulatePrepare' => true,
            'enableProfiling' => true,
            'enableParamLogging' => true,
            'username' => 'XXXXXXXX',
            'password' => 'XXXXXXXX',
            'charset' => 'utf8',
        ),
		
        'errorHandler'=>array(
            'errorAction'=>'home/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                    'categories'=>'system.*, application.*',
                ),
                array (
                    'class' => 'CProfileLogRoute',
                    'categories' => 'ajax, system.db.CDbCommand',
                    'report' => 'summary',
                    'enabled' => true,
                    'levels'=>'trace, profile, info',
                )
            ),
        ),
        
        'zend'=>array(
            'class'=>'application.extensions.zend.EZendAutoloader',
        ),
            
        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js' => false,
			)),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
   
    'params'=>array(
		'commonMedia' => array(
			// this is used in contact page
			'adminEmail'=>'admin@keytag.com',
			// Video and Image uploading and converting parameters
			'maxFileSize'=> 50000000,
			'convertWidth' => 480,
			'convertHeight' => 270,
			'imageUploadPath'=>'/public/files/image/',
			'imageThumbnailPath'=>'/public/files/image/thumbnail/',
			'videoUploadOriginalPath'=>'/public/files/video/original/',
			'videoUploadConvertedPath'=>'/public/files/video/converted/',
			'videoThumbnailPath'=>'/public/files/video/thumbnail',
			'transcoderPath'=> '/usr/bin/ffmpeg', //'C:/mencoder/mencoder.exe',
			'encodeCommand'=> '!cmd_path -i !videofile -s !widthx!height -r 15 -b 250 -ar 22050 -ab 48 !convertfile', //'!cmd_path -forceidx -of lavf -oac mp3lame -lameopts abr:br=56 -srate 22050 -ovc lavc -lavcopts vcodec=flv:vbitrate=250:mbd=2:mv0:trell:v4mv:cbp:last_pred=3 -vf scale=!width:!height -o !convertfile !videofile',
			'videoThumnbailCommand'=>'-i !videofile -an -y -f mjpeg -ss !seek -vframes 1 !thumbfile'
		),
		'ionCmSynchronization' => array(
			'cmServiceUrl' => 'http://www.curiositymachine.org/index.php?r=amfServer/index',
			'cmUserName' => 'XXXXXXXX',
			'cmPassword' => 'XXXXXXXXX',
		),
	),
);  