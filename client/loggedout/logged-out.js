jQuery(function(){

	var $ = jQuery;


    // validate login
	$("#loginform").submit(function(){
        var isFormValid = true;

        $("input").each(function(){
            if ($.trim($(this).val()).length == 0){
                $(this).addClass("submit_error");
                isFormValid = false;
            }		 
            else {
                $(this).removeClass("submit_error");
            }
        });

        return isFormValid;
    });

});