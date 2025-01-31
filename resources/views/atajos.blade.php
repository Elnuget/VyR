<script>
    // Agrega un 'event listener' al documento para escuchar eventos de teclado
    document.addEventListener('keydown', function(event) {
        console.log(event.key);
        if (event.key === "Home") {
            window.location.href = '{{ route("pedidos.create") }}'; // Crear nuevo pedido
        } else if (event.key === "End") {
            window.location.href = '{{ route("historiales_clinicos.create") }}'; // Nuevo historial clínico
        } else if (event.key === "1") {
            window.location.href = '/dashboard'; 
        }else if (event.key === "2") {
            window.location.href = '/admin'; 
        }else if (event.key === "3") {
            window.location.href = '/Pedidos'; 
        }else if (event.key === "4") {
            window.location.href = '/Pacientes'; 
        }else if (event.key === "5") {
            window.location.href = '/Inventario'; 
        }else if (event.key === "6") {
            window.location.href = '/Pagonuevos';
        }
        else if (event.key === "7") {
            window.location.href = '/Configuracion/Usuarios'; 
        }else if (event.key === "8") {
            window.location.href = '/Configuración/MediosDePago';
        }
        // Agrega más condiciones según tus necesidades
    });
</script>