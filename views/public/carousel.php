<?php
$captionPosition = isset($params['captionLocation'])
    ? html_escape($params['captionLocation'])
    : 'left';
$showDesc = isset($params['showDescr'])
    ? html_escape($params['showDescr'])
    : 'false';
$float = isset($params['float'])
	? html_escape($params['float'])
	: 'left';
$width = isset($params['width'])
	? html_escape($params['width'])
	: '95%';
$Nav = isset($params['noNav'])
	? html_escape($params['noNav'])
	: 'true';
if ($Nav == 'true' || $configs['autoPlay'] == 'true'){
	$shoArrows = 'false';
}else{
	$shoArrows = 'true'; 
};
if ($Nav == 'true'){
	$setPos = 'block';
}else{
	$setPos = 'none';
};
if ($width != '100%'){
	$tempwidth = '95%';
}else{
	$tempwidth = '100%';
};
?>

<style>
.item-file {
    width: 100%;
}

.state-img {
    padding: 0.5em; 
    border-radius: 
    5px !important; 
    color: white; 
    background-color: #5f5f5f; 
    opacity: 0.8; 
    position: absolute; 
    z-index: 1
}
</style>

<div style="max-width:100%; max-height:100%; width:<?php echo $width;?>; float:<?php echo $float;?>; ">
	<div class="carousel-stage" aria-label="Image carousel. Use arrow keys to advance and the k key to toggle pause or play." style="max-width:100%; max-height:100%; width:<?php echo $tempwidth;?>;" >
		<?php foreach($items as $item):
			set_current_record('Item', $item);
			if (metadata($item,'has files'))
			{ 
				$itemFiles = $item->Files;
				foreach($itemFiles as $itemfile):?>
				<div>
					<?php echo file_markup($itemfile,array('imageSize' => 'fullsize','linkToFile' => true, 'imgAttributes' => array('max-height'=>'100%','max-width'=>'100%','width'=>'100%')));?>
					<?php if ($captionPosition != 'none'){ ?>
						
					<p class="desc caption-<?php echo $captionPosition; ?>">			
						<?php if (metadata($itemfile,array('Dublin Core','Title'))){
							echo metadata($itemfile,array('Dublin Core','Title'));
						}else{
							echo html_escape(metadata($item, array('Dublin Core', 'Title'))),' ';    
						}?>
					</p>
					<?php }?>
								
						<?php if ($showDesc == 'true'):?>
								<div id="item-metadata">
									<?php echo all_element_texts('item'); ?>
								</div>
						<?php endif; ?>	
				</div>
				<?php endforeach; ?>
			<?php }; ?>
		<?php endforeach; ?>
	</div>
</div>
	
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.carousel-stage')
		        .on('init', function(event, slick){
					jQuery('img.full')[0].onmouseover = (function() {
					    var onmousestop = function() {
					       jQuery('input.slick-next').css('display', 'none');
					       jQuery('input.slick-prev').css('display', 'none');
					    }, thread;

				    return function() {
				       jQuery('input.slick-next').css('display', 'block');
				       jQuery('input.slick-prev').css('display', 'block');
					        clearTimeout(thread);
					        thread = setTimeout(onmousestop, 2000);
					    };
					})();
		        });

        player = jQuery('.carousel-stage').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: <?php echo $shoArrows;?>,
            fade: true,
            centerMode: true,
            variableWidth: false,
            adaptiveHeight: true,
            asNavFor: '.carousel-navigation',
            prevArrow: '<input class="slick-prev" type="submit" value="&laquo;" />',
            nextArrow: '<input class="slick-next" type="submit" value="&raquo;" />',
            dots: false,
            autoplay: <?php echo $configs['autoPlay'];?>,
            autoplaySpeed: <?php echo $configs['autoplaySpeed'];?>,
            focusOnSelect: <?php echo $configs['focusOnSelect'];?>,
            arrows: <?php echo $configs['arrows'];?>,
            pauseOnHover: false,
            pauseOnDotsHover: false,
            pauseOnFocus: false,
        });

        jQuery(document).keypress(function(event) {
            console.log(event);
            //Dont slide if the cursor is inside the form fields and arrow keys are pressed
            if(!event.target.tagName.match('TEXTAREA|INPUT|SELECT')) {
                if (event.keyCode === 107 && player['0'].slick.options.accessibility === true) {
                    if (player['0'].slick.paused) {
                        play();
                    }
                    else {
                        pause();
                    }
                }
            }
        }); 
	
		jQuery('.carousel-stage').on('afterChange',function(event, slick, currentSlide, nextSlide){
			
            jQuery('img.full')[jQuery('.carousel-stage').slick('slickCurrentSlide')].onmouseover = (function() {
                var onmousestop = function() {
                    jQuery('input.slick-next').css('display', 'none');
                    jQuery('input.slick-prev').css('display', 'none');
                }, thread;

                return function() {
                    jQuery('input.slick-next').css('display', 'block');
                    jQuery('input.slick-prev').css('display', 'block');
                        clearTimeout(thread);
                        thread = setTimeout(onmousestop, 2000);
                    };
            })();
        });
	
        jQuery('.slick-slide').append("<span class='state-img fas fa-pause-circle fa-3x'></span>");
        jQuery('.state-img').hide();
        jQuery('.carousel-stage').data('state', 'playing');

        jQuery('.slick-slide').mouseover(function(e) {
            jQuery('.state-img').show();
            positionToggle(e);
        });
        
        jQuery('.slick-slide').on('focus', (function(e) {
            jQuery('.state-img').show();
            positionToggle(e);
        }));
 
        jQuery('.slick-slide').mouseout(function(e) {
            jQuery('.state-img').hide();
        });
       
        jQuery('.slick-slide').off('focus', (function(e) {
            jQuery('.state-img').hide();
        }));

        jQuery('.slick-slide').click(function(e) {
            e.preventDefault();
            if (player['0'].slick.paused) {
                play();
            }
            else {
                pause();
            }
        });
	
    });

    function positionToggle(e) {
        var element = jQuery('div.slick-slide.slick-current.slick-active');

        var height = element.height();
        var img_height = (height / 2) - 45;
        jQuery('.state-img').css('top', img_height);
        jQuery('.state-img').css('left', "50%");
    }

    function play() {
        jQuery('.carousel-stage').slick('slickPlay');
        jQuery('.state-img').removeClass('fa-play-circle');
        jQuery('.state-img').addClass('fa-pause-circle');
    }
   
    function pause() {
        jQuery('.carousel-stage').slick('slickPause');
        jQuery('.state-img').removeClass('fa-pause-circle');
        jQuery('.state-img').addClass('fa-play-circle');
    }
</script>
