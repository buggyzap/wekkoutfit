$(document).ready(function(){
  $(".week_viewport").addClass("loading");
})
function initSliding(){
  $(".next,.prev").off();
  var height = $(".week_item").first().outerHeight();
  $(".week_viewport").css("height",height+"px");
  $(".next").on("click",function(){
    if($(".week_item").not(".faded").next().hasClass("week_item")){
      var next = $(".week_item").not(".faded").next();
      $(".week_item").not(".faded").addClass("faded");
      next.removeClass("faded");
    }else{
      $(".week_item").addClass("faded");
      $(".week_item").first().removeClass("faded");
    }
  })
  $(".prev").on("click",function(){
    if($(".week_item").not(".faded").prev().hasClass("week_item")){
      var prev = $(".week_item").not(".faded").prev();
      $(".week_item").not(".faded").addClass("faded");
      prev.removeClass("faded");
    }else{
      $(".week_item").addClass("faded");
      $(".week_item").last().removeClass("faded");
    }
  })
}
$(window).load(function(){
  $(".week_item").addClass("faded");
  $(".week_item").first().removeClass("faded");
  $(".week_viewport").removeClass("loading");
  initSliding();
});
$(window).resize(function(){
  initSliding();
});
