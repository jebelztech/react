jQuery(document).ready(function(){
    alert("hereeeeee");

    jQuery("#FreshProducts").hide();
    jQuery("#Sweets").hide();
    alert("sdfsdf");
    jQuery(".vertical-menu-wrapper .nav-item a[href='#Sweets']").hover(function(){
        // alert("sweet");
        jQuery("#FreshProducts").hide();
        jQuery("#RamadanOffers").hide();
        jQuery("#Sweets").show();
    });
    jQuery(".vertical-menu-wrapper .nav-item a[href='#FreshProducts']").hover(function(){
        // alert("product");
        jQuery("#FreshProducts").show();
        jQuery("#Sweets").hide();
        jQuery("#RamadanOffers").hide();
    });
    jQuery(".vertical-menu-wrapper .nav-item a[href='#RamadanOffers']").hover(function(){
        // alert("product");
        jQuery("#FreshProducts").hide();
        jQuery("#Sweets").hide();
        jQuery("#RamadanOffers").show();
    });
 
   
  });
