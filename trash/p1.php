<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Root Fitness Studio</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Header / Navbar -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-green-600">ROOT</h1>
      <nav class="space-x-6">
        <a href="#sobre" class="hover:text-green-600">Sobre</a>
        <a href="#servicos" class="hover:text-green-600">Serviços</a>
        <a href="#horarios" class="hover:text-green-600">Horários</a>
        <a href="#localizacao" class="hover:text-green-600">Localização</a>
        <a href="#contacto" class="hover:text-green-600">Contacto</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="bg-green-600 text-white py-20 text-center">
    <h2 class="text-4xl font-bold mb-4">Desperta o teu potencial no Root Studio</h2>
    <p class="text-lg mb-6">Treinos personalizados, ambiente motivador e foco no teu bem-estar.</p>
    <a href="#contacto" class="bg-white text-green-600 px-6 py-3 rounded-full font-semibold">Marca já a tua sessão</a>
  </section>

  <!-- Sobre -->
  <section id="sobre" class="py-16 max-w-5xl mx-auto px-4">
    <h3 class="text-3xl font-bold mb-4 text-center">Sobre o Root</h3>
    <p class="text-lg text-center">Somos um estúdio de fitness focado em treino funcional, bem-estar e transformação pessoal. Acreditamos que raízes fortes criam corpos e mentes resilientes.</p>
  </section>

  <!-- Serviços -->
  <section id="servicos" class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-4">
      <h3 class="text-3xl font-bold mb-10 text-center">Nossos Serviços</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <h4 class="text-xl font-semibold mb-2">Treino Funcional</h4>
          <p>Melhora força, flexibilidade e resistência com exercícios adaptados a ti.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <h4 class="text-xl font-semibold mb-2">Personal Trainer</h4>
          <p>Acompanhamento individual para alcançar os teus objetivos.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <h4 class="text-xl font-semibold mb-2">Aulas em Grupo</h4>
          <p>Motivação em conjunto com treinos dinâmicos e divertidos.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Horários -->
  <section id="horarios" class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
      <h3 class="text-3xl font-bold mb-6 text-center">Horários</h3>
      <table class="w-full text-left border border-gray-300">
        <thead>
          <tr class="bg-green-600 text-white">
            <th class="p-3">Dia</th>
            <th class="p-3">Abertura</th>
            <th class="p-3">Fecho</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t">
            <td class="p-3">Seg - Sex</td>
            <td class="p-3">07:00</td>
            <td class="p-3">21:00</td>
          </tr>
          <tr class="border-t">
            <td class="p-3">Sábado</td>
            <td class="p-3">09:00</td>
            <td class="p-3">13:00</td>
          </tr>
          <tr class="border-t">
            <td class="p-3">Domingo</td>
            <td class="p-3">Fechado</td>
            <td class="p-3">-</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Localização -->
  <section id="localizacao" class="py-16">
    <div class="max-w-5xl mx-auto px-4 text-center">
      <h3 class="text-3xl font-bold mb-4">Onde Estamos</h3>
      <p class="mb-6">Rua das Raízes 123, Lisboa</p>
      <iframe class="w-full h-64 rounded shadow"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3110.123456789!2d-9.123456!3d38.716891!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd193333333333%3A0xabcdefabcdef!5e0!3m2!1spt-PT!2spt!4v171234567890!5m2!1spt-PT!2spt"
        allowfullscreen="" loading="lazy"></iframe>
    </div>
  </section>

  <!-- Contacto -->
  <section id="contacto" class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4">
      <h3 class="text-3xl font-bold mb-6 text-center">Fala Connosco</h3>
      <form class="space-y-4">
        <input type="text" placeholder="Nome" class="w-full p-3 border rounded" required />
        <input type="email" placeholder="Email" class="w-full p-3 border rounded" required />
        <textarea placeholder="Mensagem" rows="4" class="w-full p-3 border rounded" required></textarea>
        <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded font-semibold hover:bg-green-700">Enviar</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white text-center py-6 mt-12">
    <p>&copy; 2025 Root Fitness Studio. Todos os direitos reservados.</p>
  </footer>

</body>
</html>
