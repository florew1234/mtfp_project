$('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    autoplay:true,
    //autoplayTimeout:1000,
    //autoplayHoverPause:true
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:4
        }
    }
})

function confirmation(service)
{
    Swal.fire({
        text: "Inscrivez-vous et authentifiez-vous pour acceder au service "+service,
        icon: 'warning',
        title: 'Service en ligne',
        showCancelButton: true,
        confirmButtonText: 'Poursuivre',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
             window.open("http://portailmtfp.hebergeappli.bj", '_blank')
            }else{
                result.dismiss === Swal.DismissReason.cancel
            }
        }, function (dismiss) {
            return false;
        })
}