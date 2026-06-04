<section class="container mx-auto px-6 py-12">
    <div class="bg-leafSoft rounded-3xl p-8 lg:p-10 flex flex-col md:flex-row items-center justify-between gap-6 border border-green-100">
        <div class="text-center md:text-left">
            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">Siap wujudkan pernikahan impian Anda?</h3>
            <p class="text-gray-600 text-sm">Konsultasikan kebutuhan Anda bersama tim Brilliant — gratis & tanpa komitmen.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center bg-bottle text-white font-bold py-3 px-8 rounded-full hover:bg-bottleHover transition">
                Konsultasi Sekarang
            </a>
            <a href="{{ \App\Support\Branding::whatsappUrl('Halo Brilliant, saya ingin konsultasi pernikahan') }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center border-2 border-bottle text-bottle font-bold py-3 px-8 rounded-full hover:bg-white transition">
                WhatsApp
            </a>
        </div>
    </div>
</section>
