jQuery(document).ready(function($) {

    $(".btn_block_cliente").on('click', function(){
        let pedido_id = $(this).attr('data-pedido');
        let cliente_id = $(this).attr('data-cliente');


        Swal.fire({
  title: '¿Está seguro?',
  text: "Esta acción no tiene retorno",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Estoy seguro'
}).then((result) => {
  if (result.isConfirmed) {
    // aca el post para bloquear
    var data = {
        'action': 'bloquear_usuario_accion',
        'pedido_id': pedido_id,
        'cliente_id': cliente_id
    };

    jQuery.post(ajax_object.ajax_url, data, function(response) {
      location.reload();

  //  alert(response);
    });

    // hasta aca la accion
  }
});

    })
}) ;
