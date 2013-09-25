jQuery(document).ready( function() {
	// Put your JS in here, and it will run after the DOM has loaded.
	// jQuery.post( ajaxurl, {
	// 	action: 'my_example_action',
	// 	'cookie': encodeURIComponent(document.cookie),
	// 	'parameter_1': 'some_value'
	// }, 
	// function(response) { 
	// 	... 
	// } );

	jQuery('.like_album, .like_image').on('click', function() {
		
		var type = jQuery(this).attr('class');
		var id = jQuery(this).attr('id');
		
		jQuery(this).addClass('loading');
		
		jQuery.post( ajaxurl, {
			action: 'BPGPLSAlbumLike',
			'cookie': encodeURIComponent(document.cookie),
			'type': type,
			'id': id
		},
		function(data) {
			
			jQuery('#' + id).fadeOut( 100, function() {
				jQuery(this).html(data).removeClass('loading').fadeIn(100);
			});
			
			// Swap from like to unlike
			if (type == 'like') {
				var newID = id.replace("like", "liked");
				if(type == 'like_image')
				{
					jQuery('#' + id).removeClass('like_image').addClass('liked_image').attr('title', 'You like this item').attr('id', newID);
				}
				else
				{
					jQuery('#' + id).removeClass('like_album').addClass('liked_album').attr('title', 'You like this item').attr('id', newID);
				}
				
			}
						
		});
		
		return false;
	});
		jQuery('#selected_album').change(function() {
  	var albumID = jQuery('#selected_album').val();
	  	jQuery('#selected_group_album').val(0);
  		jQuery('#album_name').val('');
			jQuery('#album_visibility').css('display','block');
  		jQuery('#priv_2').attr('checked', 'checked');
  	BPGPLSAlbumPrivacy(albumID);
	});
		jQuery('#selected_group_album').change(function(){
  		jQuery('#selected_album').val(0);
  		jQuery('#album_name').val('');
  		jQuery('#priv_5').attr('checked', 'checked');
			jQuery('#album_visibility').css('display','none');
		});
		jQuery('#album_name').change(function(){
  	jQuery('#selected_album').val(0);
  	jQuery('#selected_group_album').val(0);
			jQuery('#album_visibility').css('display','block');
  		jQuery('#priv_2').attr('checked', 'checked');
		});
});

	function BPGPLSDeleteAlbum(theAlbumID, theAlbumTitle)
	{
		if(confirm("Are you sure you want to delete album "+theAlbumTitle+" and all it's contents"))
		{
				ShowLoadingScreen("Please wait while the album and all it's contents is deleted");
				jQuery.post(
									BPGPLSAjax.ajaxurl,
									{
										action: 'BPGPLSDeleteAlbum',
										albumID: theAlbumID,
										BPGPLSDeleteAlbumNonce: BPGPLSAjax.BPGPLSDeleteAlbum
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPGPLSAjaxSuccess = true;
												}
												else
												{
													BPGPLSAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPGPLSAjaxSuccess)
									{
										alert('Album "'+theAlbumTitle+'" Deleted Successfully ');
										window.location.reload(true);
									}
									else
									{
										alert('Unable to delete album "'+theAlbumTitle+'"');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPGPLSDeleteImage(theImageID)
	{
		if(confirm("Are you sure you want to delete this image"))
		{
				ShowLoadingScreen('Please wait while your image is deleted');
					jQuery.post(
									BPGPLSAjax.ajaxurl,
									{
										action: 'BPGPLSDeleteImage',
										imageID: theImageID,
										BPGPLSDeleteImageNonce: BPGPLSAjax.BPGPLSDeleteImage
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPGPLSAjaxSuccess = true;
												}
												else
												{
													BPGPLSAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPGPLSAjaxSuccess)
									{
										alert('Image Deleted Successfully ');
										window.location.reload(true);
									}
									else
									{
										alert('Unable to delete Image');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPGPLSFeatureImage(theAlbumID,theAlbumTitle, theImageID)
	{
		if(confirm("Are you sure you want to make this image the feature image for album '"+theAlbumTitle+"'"))
		{
				ShowLoadingScreen('Please wait while the new feature image is set');
				jQuery.post(
									BPGPLSAjax.ajaxurl,
									{
										action: 'BPGPLSFeatureImage',
										albumID: theAlbumID,
										imageID: theImageID,
										BPGPLSFeatureImageNonce: BPGPLSAjax.BPGPLSFeatureImage
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPGPLSAjaxSuccess = true;
												}
												else
												{
													BPGPLSAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPGPLSAjaxSuccess)
									{
										alert("Image Set Successfully As Feature Image for '"+theAlbumTitle+"'");
										window.location.reload(true);
									}
									else
									{
										alert('Unable To Set Feature Image');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPGPLSAlbumPrivacy(theAlbumID)
	{
		jQuery.get(
									BPGPLSAjax.ajaxurl,
									{
									action: 'BPGPLSAlbumPrivacy',
										albumID: theAlbumID,
										BPGPLSAlbumPrivacyNonce: BPGPLSAjax.BPGPLSAlbumPrivacy
										},
									function(response){
												if (response[0].result.indexOf('success') != -1)
												{
													jQuery('#priv_2').prop('checked',false);
													jQuery('#priv_3').prop('checked',false);
													jQuery('#priv_4').prop('checked',false);
													jQuery('#priv_6').prop('checked',false);
													var new_priv = 'priv_'+ response[0].privacy[0].privacy;
													jQuery('#' + new_priv).prop('checked',true);
													if(response[0].privacy[0].privacy == 3) // group privacy
													{
														jQuery('#selected_group').val(response[0].privacy[0].groupID);
													}
												}
												else
												{
													jQuery('#priv_2').prop('checked',true);
												}
									}, "jsonp")	
 								.success(function() {	
					  		})
								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								});

	}
	
function ShowLoadingScreen(txtMessg){
		var maskHeight = jQuery(document).height();
    var maskWidth = jQuery(window).width();
    jQuery('.padder').append( '<div id="loadmask"style="text-align: center;"><p><H3>'+txtMessg+'</H3></p></div>'); 
    //Set height and width to mask to fill up the whole screen
//    jQuery('#loadmask').css({'width':maskWidth,'height':maskHeight});
    
		//transition effect     
		jQuery('#loadmask').fadeIn(1000);    
		jQuery('#loadmask').fadeTo(10,0.5);  
}

function HideLoadingScreen(){
		jQuery('#loadmask').hide();
		jQuery('#loadmask').remove();    
}

