AOS.init();

function mobileMenuOpen() {
    document.getElementById("cusDropdownList").classList.toggle("show");
}
window.onclick = function(event) {
  if (!event.target.matches('.cusDropdown')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}

$(document).ready(function(){

    // component box scripts
    $('.componentBox input:checkbox').change(function(){
        if($(this).is(':checked'))
            $(this).parents('.componentBox').addClass('active');
        else
            $(this).parents('.componentBox').removeClass('active');
    })

    // input spinner filed
    $('.qtyBtn').on('click', function(){
      var $myButton = $(this);
      var oldVal = $myButton.parent().find('input').val();
      if($myButton.text() == '+'){
        var newVal = parseFloat(oldVal) + 1;
      }else{
        if(oldVal > 1){
          var newVal = parseFloat(oldVal) - 1;
        }else{
          newVal = 1;
        }
      }
      $myButton.parent().find('input').val(newVal);

    })





// // accordion script
// var acc = document.getElementsByClassName("accordion");
// var i;

// for (i = 0; i < acc.length; i++) {
//   acc[i].addEventListener("click", function() {
//     this.classList.toggle("active");
//     var panel = this.nextElementSibling;
//     if (panel.style.maxHeight) {
//       panel.style.maxHeight = null;
//     } else {
//       panel.style.maxHeight = panel.scrollHeight +30*4+ "px";
//     } 
//   });
// }
// document.getElementById("accDefaultOpen").click();

});


// tab script
function openTab(e, tabName){
  var i, tabContent, tabLinks;
  tabContent = document.getElementsByClassName("tabContent");
  for( i=0; i<tabContent.length; i++){
    tabContent[i].style.display="none";    
  }

  tabLinks = document.getElementsByClassName("tabLinks");
  for( i=0; i<tabLinks.length; i++){
    tabLinks[i].className = tabLinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display="block";
  e.currentTarget.className +=" active";
}
document.getElementById("tabDefaultOpen").click();


 jQuery(document).ready(function () {
//change selectboxes to selectize mode to be searchable
   jQuery("select").select2();
});