<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Copa Gol</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease-in-out;
        }

        .whatsapp-float:hover {
            background-color: #1DA851;
            transform: scale(1.1);
        }

        .whatsapp-float i {
            margin-top: 0; /* Adjust icon vertical alignment */
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">

    <div class="bg-azul text-white">
        <header class="fixed top-0 left-0 right-0 z-50 bg-azul/80 backdrop-blur-sm shadow-md">
            <div class="container mx-auto flex justify-between items-center p-4">
                <h1 class="text-2xl font-bold">Copa Gol</h1>
                <nav class="hidden md:flex space-x-6 items-center">
                    <a href="#home" class="hover:text-naranja transition">Inicio</a>
                    <a href="#noticias" class="hover:text-naranja transition">Noticias</a>
                    <a href="#acerca" class="hover:text-naranja transition">Acerca de</a>
                    <a href="#planes" class="hover:text-naranja transition">Planes</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded-lg">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-naranja transition">Ingresar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded-lg">Regístrate</a>
                            @endif
                        @endauth
                    @endif
                </nav>
                <div class="md:hidden">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden">
                <a href="#home" class="block py-2 px-4 text-sm hover:bg-azul-dark">Inicio</a>
                <a href="#noticias" class="block py-2 px-4 text-sm hover:bg-azul-dark">Noticias</a>
                <a href="#acerca" class="block py-2 px-4 text-sm hover:bg-azul-dark">Acerca de</a>
                <a href="#planes" class="block py-2 px-4 text-sm hover:bg-azul-dark">Planes</a>
                 @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block py-2 px-4 text-sm hover:bg-azul-dark">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block py-2 px-4 text-sm hover:bg-azul-dark">Ingresar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block py-2 px-4 text-sm hover:bg-azul-dark">Regístrate</a>
                        @endif
                    @endauth
                @endif
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="min-h-screen flex items-center justify-center text-center bg-cover bg-center" style="background-color: #2A3A5B;">
            <div class="bg-azul bg-opacity-70 p-10 rounded-lg">
                <h2 class="text-5xl font-extrabold mb-4">La Pasión del Fútbol en un Solo Lugar</h2>
                <p class="text-xl mb-8">Crea, gestiona y compite en tus propios torneos de fútbol.</p>
                <a href="{{ route('register') }}" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-3 px-8 rounded-lg text-lg transition">Regístrate ahora</a>
                <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition ml-4">Iniciar Sesión</a>
            </div>
        </section>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Noticias y Campeones Section -->
        <section id="noticias" class="py-20 bg-white">
            <div class="container mx-auto text-center">
                <h3 class="text-4xl font-bold mb-12 text-azul">Noticias y Campeones</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Placeholder Card -->
                    <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                        <h4 class="font-bold text-xl mb-2">Últimos Campeones</h4>
                        <p>Galería de los equipos que levantaron la copa.</p>
                    </div>
                    <!-- Placeholder Card -->
                    <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                        <h4 class="font-bold text-xl mb-2">Noticia Destacada</h4>
                        <p>El torneo de verano rompe récords de participación.</p>
                    </div>
                    <!-- Placeholder Card -->
                    <div class="bg-gray-100 p-6 rounded-lg shadow-lg">
                        <h4 class="font-bold text-xl mb-2">Próximos Eventos</h4>
                        <p>Anuncio de la nueva Copa de Invierno 2025.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Acerca de Nosotros Section -->
        <section id="acerca" class="py-20 bg-azul text-white">
            <div class="container mx-auto text-center max-w-4xl">
                <h3 class="text-4xl font-bold mb-6">Nuestra Misión</h3>
                <p class="text-lg leading-relaxed">
                    En Copa Gol, nuestra misión es potenciar la organización de torneos de fútbol amateur, ofreciendo una plataforma digital completa, intuitiva y profesional. Queremos que cada organizador, delegado y jugador viva la mejor experiencia, desde la creación de un campeonato hasta la gran final.
                </p>
            </div>
        </section>

        <!-- Futuros Planes Section -->
        <section id="futuro" class="py-20 bg-white">
            <div class="container mx-auto text-center">
                <h3 class="text-4xl font-bold mb-12 text-azul">Próximamente</h3>
                 <div class="bg-gray-100 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <h4 class="font-bold text-xl mb-2">Buscador y Alquiler de Canchas</h4>
                    <p>Estamos trabajando en una nueva funcionalidad que te permitirá encontrar y alquilar canchas directamente desde la plataforma, conectando a los equipos con proveedores de espacios deportivos.</p>
                </div>
            </div>
        </section>

        <!-- Planes y Beneficios Section -->
        <section id="planes" class="py-20 bg-gray-100">
            <div class="container mx-auto">
                <h3 class="text-4xl font-bold text-center mb-12 text-azul">Planes y Beneficios</h3>
                <div class="flex flex-wrap justify-center gap-8">
                    <!-- Opción 1: Modelo "Gratis Ilimitado" -->
                    <div class="w-full md:w-1/2 p-8 bg-white rounded-lg shadow-lg border-2 border-gray-200">
                        <h4 class="text-2xl font-bold text-center mb-4">Acceso Completo basico BETA</h4>
                        <p class="text-center text-gray-600 mb-6">¡Experimenta Copa Gol sin límites!</p>
                        <ul class="space-y-4 text-gray-700">
                            <li><span class="text-green-500 mr-2">&#10003;</span>Crea y gestiona torneos </li>
                            <li><span class="text-green-500 mr-2">&#10003;</span>Acceso a todas las herramientas de gestión</li>
                            <li><span class="text-green-500 mr-2">&#10003;</span>Soporte prioritario durante la fase de prueba</li>
                            <li><span class="text-green-500 mr-2">&#10003;</span>Invitación a ser parte de la comunidad de lanzamiento</li>
                        </ul>
                        <div class="text-center mt-8">
                            <a href="{{ route('register') }}" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-3 px-8 rounded-lg text-lg transition">Regístrate ahora, es 100% gratis</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-azul text-white text-center p-4">
        <p>&copy; {{ date('Y') }} Copa Gol. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/51941523331" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-whatsapp"></i>
    </a>

</body>
</html>