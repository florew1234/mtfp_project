import Swal from 'sweetalert2'

export const AlertNotif: any = {

 
    finish(title,msg,type){
        return Swal.fire(
            title,
            msg,
            type
          )
      },
    affiche(titl,msg){
        return Swal.fire({
          position: 'top-end',
          title: titl,
          text: msg,
        })
      },
      finishConfirm(head,msg){
        return Swal.fire({
            title: head,
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
          })      
    }
    
}