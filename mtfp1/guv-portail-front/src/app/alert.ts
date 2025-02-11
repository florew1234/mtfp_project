import Swal from 'sweetalert2'

export const AlertNotif: any = {

 
    finish(title,msg,type){
        return Swal.fire(
            title,
            msg,
            type
          )
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
    },
      MsgToast(){
        return Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 15000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
    },
      MsgToastInfos(head){
        return Swal.fire({
          title: head,
          showClass: {
            popup: 'animate__animated animate__fadeInDown'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
          }
        })
      }
}