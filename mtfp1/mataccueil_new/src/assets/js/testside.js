
setTimeout(function (){
    console.log("________________recu____________________")
    $('.button-collapse').sideNav();
    new WOW().init();
  },2000)


setTimeout(function () {
    console.log("________________Recu_Toop____________________")
    $("[title]").tooltip({
        container: "body",
        placement: "top",
        delay: { show: 240, hide: 60 }
    });

    $("[title]").on("click", function () {
        $(this).tooltip("hide");
    });
}, 545*1.33); // 545*1.33    545ms timing to load jQuery.js + network estimated delay 