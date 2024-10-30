jQuery(document).ready(function(){
     var imgid = [];
     if(jQuery('#img_ids').val() )
     imgid.push(jQuery('#img_ids').val());
    else
        jQuery('#close').css('display', 'none');
      
     jQuery("#btnImage").on("click", function() {
        var images = wp.media({
            title: "Upload Image",
            multiple: true
        }).open().on("select", function(e) {
            var uploadedImages = images.state().get("selection");
            var selectedImages = uploadedImages;
           
           

            //console.log(selectedImages.title + "  " + selectedImages.url + "   " + selectedImages.filename);

            selectedImages.map(function(image){
               
             var itemDetails = image.toJSON();
             jQuery('.gallery').append('<div class="single_img"><img src="'+itemDetails.url+'" width="75" height="75"  /> <a href="javascript:;" id="close" data-value="'+itemDetails.id+'"><span class="close">X</span></a></div>');
           // imgid.push(jQuery('#img_ids').val(imgid));

            imgid.push(itemDetails.id);
             console.log(imgid);
             //jQuery('#img_ids').val();
              jQuery('#img_ids').val(imgid);
            
            
           
             });

          
        });
    });
       jQuery(document).on("click", "#close" , function() {
         var output = removeValue( jQuery('#img_ids').val(), jQuery(this).attr('data-value'));
          jQuery('#img_ids').val(output);
            jQuery(this).parent().remove();
        });

       jQuery("#submit").click(function(event){

            event.preventDefault(); // Prevent the default form submit.            
            
            var myCheckboxes = new Array();
            jQuery("input:checked").each(function() {
               myCheckboxes.push(jQuery(this).val());
            });
          
           var data = 
            {
                'action' : 'mig_gallery_form_response',
                'postTypes': myCheckboxes
               
            };
            if(myCheckboxes.length > 0){
            jQuery.post( params.ajaxurl, data, function( response ) {
                
                jQuery('.msg_success').html('<div class="notice notice-success mig_msg">Updated Successfully</div>');
            });
            } else{
                     jQuery('.msg_success').html('<div class="notice notice-error mig_msg">Please select post type</div>');
            }
        
       });
 });

 function removeValue(list, value) {
if( list.indexOf(','+value+',') != -1 ){
return list.replace(new RegExp(',?'+ value + ',?'), ',');
}
else{
return list.replace(new RegExp(',?'+ value + ',?'), '')
}
}