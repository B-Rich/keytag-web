<?php
	
	/**
	 * JwPlayer	用于插入视频
	 * @author istrone
	 */
	class JwPlayer extends CWidget {
				
		public $flvName;
		
		public $rtmpPath;		//	rtmp://istrone.com/jwplayer
		
		public $width = 550;
		
		public $height = 400 ;
		
		public $id;//= 'media';

		private $_path ;

		public $autoStart = false;
		
		public function init(){
			$this->_path =  dirname(__FILE__);
			?>
			<script type="text/javascript" src="<?php echo CHtml::asset($this->_path.'/jwplayer.js')?>"></script>
			<?php 
			parent::init();
		}
		
		public function run(){
			$path =  dirname(__FILE__);
			?>
			<div id="<?php echo $this->id;?>"></div>
			<script type="text/javascript">
			jwplayer("<?php echo $this->id?>").setup({
					autostart: <?php echo $this->autoStart?'true':'false'?> ,
					flashplayer: "<?php echo CHtml::asset($this->_path.'/player.swf')?>",
					file:'<?php echo $this->flvName;?>',
					//streamer: '<?php echo $this->rtmpPath?>',
					skin:'<?php echo CHtml::asset($this->_path.'/glow.zip')?>',
					width:<?php echo $this->width?>,
					height:<?php echo $this->height?>,
					bufferlength:1,
					smoothing:true
				});
				</script>
			<?php 
		}
		
	}