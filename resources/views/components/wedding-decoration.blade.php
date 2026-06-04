<!-- Wedding Floral Decorations -->
<style>
    @keyframes floatLeft {
        0%, 100% { transform: translateX(0px) translateY(0px) rotate(0deg); opacity: 0.6; }
        25% { transform: translateX(-15px) translateY(-10px) rotate(5deg); opacity: 0.7; }
        50% { transform: translateX(0px) translateY(-20px) rotate(10deg); opacity: 0.6; }
        75% { transform: translateX(15px) translateY(-10px) rotate(5deg); opacity: 0.7; }
    }

    @keyframes floatRight {
        0%, 100% { transform: translateX(0px) translateY(0px) rotate(0deg); opacity: 0.6; }
        25% { transform: translateX(15px) translateY(-10px) rotate(-5deg); opacity: 0.7; }
        50% { transform: translateX(0px) translateY(-20px) rotate(-10deg); opacity: 0.6; }
        75% { transform: translateX(-15px) translateY(-10px) rotate(-5deg); opacity: 0.7; }
    }

    @keyframes floatSlow {
        0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
        50% { transform: translateY(-15px) rotate(10deg); opacity: 0.7; }
    }

    @keyframes rotate360 {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes floatUp {
        0%, 100% { transform: translateY(0px); opacity: 0.4; }
        50% { transform: translateY(-10px); opacity: 0.6; }
    }

    .wedding-decoration {
        position: fixed;
        pointer-events: none;
        z-index: 1;
    }

    .flower-decoration {
        animation: floatSlow 6s ease-in-out infinite;
    }

    .leaf-decoration {
        animation: floatLeft 8s ease-in-out infinite;
    }

    .leaf-right {
        animation: floatRight 8s ease-in-out infinite;
    }

    .ring-decoration {
        animation: rotate360 20s linear infinite;
        opacity: 0.4;
    }

    .float-up {
        animation: floatUp 4s ease-in-out infinite;
    }

    /* Position utilities */
    .top-left { top: 2rem; left: 2rem; }
    .top-right { top: 2rem; right: 2rem; }
    .bottom-left { bottom: 2rem; left: 2rem; }
    .bottom-right { bottom: 2rem; right: 2rem; }
    .top-center { top: 1rem; left: 50%; transform: translateX(-50%); }
    .corner-tl { top: 0; left: 0; }
    .corner-tr { top: 0; right: 0; }
    .corner-bl { bottom: 0; left: 0; }
    .corner-br { bottom: 0; right: 0; }

    /* Responsive hiding */
    @media (max-width: 768px) {
        .wedding-decoration {
            display: none !important;
        }
    }
</style>

@if($position ?? 'all' !== 'none')
    {{-- Top Left Corner --}}
    <svg class="wedding-decoration top-left flower-decoration" width="80" height="80" viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
        <!-- Rose -->
        <defs>
            <filter id="shadow">
                <feGaussianBlur in="SourceGraphic" stdDeviation="2" />
            </filter>
        </defs>
        <circle cx="40" cy="20" r="8" fill="#D4897B" opacity="0.8"/>
        <circle cx="32" cy="28" r="6" fill="#E8A89C" opacity="0.7"/>
        <circle cx="48" cy="28" r="6" fill="#E8A89C" opacity="0.7"/>
        <circle cx="30" cy="40" r="5" fill="#F0B8AC" opacity="0.7"/>
        <circle cx="50" cy="40" r="5" fill="#F0B8AC" opacity="0.7"/>
        <circle cx="40" cy="45" r="4" fill="#FFD4C8" opacity="0.6"/>
        <!-- Stem -->
        <path d="M 40 50 Q 38 60 40 75" stroke="#8B7355" stroke-width="2" fill="none" opacity="0.7"/>
        <!-- Leaves -->
        <ellipse cx="35" cy="60" rx="3" ry="8" fill="#90C590" opacity="0.6" transform="rotate(-30 35 60)"/>
        <ellipse cx="45" cy="65" rx="3" ry="8" fill="#90C590" opacity="0.6" transform="rotate(30 45 65)"/>
    </svg>

    {{-- Top Right Corner --}}
    <svg class="wedding-decoration top-right leaf-right" width="70" height="70" viewBox="0 0 70 70" xmlns="http://www.w3.org/2000/svg">
        <!-- Leaf accent -->
        <path d="M 35 10 Q 50 25 45 50 Q 35 55 25 50 Q 20 25 35 10" fill="#A8D5A8" opacity="0.7"/>
        <path d="M 35 15 Q 43 26 40 48 Q 35 51 30 48 Q 27 26 35 15" fill="#C8E6C9" opacity="0.6"/>
        <!-- Small flowers -->
        <circle cx="15" cy="20" r="2" fill="#FFB6D9" opacity="0.8"/>
        <circle cx="18" cy="15" r="2" fill="#FFB6D9" opacity="0.7"/>
        <circle cx="12" cy="15" r="2" fill="#FFC9E3" opacity="0.6"/>
        <circle cx="55" cy="30" r="2" fill="#FFB6D9" opacity="0.8"/>
        <circle cx="60" cy="28" r="2" fill="#FFC9E3" opacity="0.7"/>
    </svg>

    {{-- Bottom Left Corner --}}
    <svg class="wedding-decoration bottom-left leaf-decoration" width="90" height="90" viewBox="0 0 90 90" xmlns="http://www.w3.org/2000/svg">
        <!-- Ring duo (intertwined) -->
        <circle cx="30" cy="45" r="15" fill="none" stroke="#D4AF37" stroke-width="3" opacity="0.6"/>
        <circle cx="50" cy="45" r="15" fill="none" stroke="#D4AF37" stroke-width="3" opacity="0.6"/>
        <!-- Intersection effect -->
        <circle cx="40" cy="45" r="3" fill="#D4AF37" opacity="0.5"/>
    </svg>

    {{-- Bottom Right Corner --}}
    <svg class="wedding-decoration bottom-right flower-decoration" width="75" height="75" viewBox="0 0 75 75" xmlns="http://www.w3.org/2000/svg">
        <!-- Floral spray -->
        <g opacity="0.7">
            <!-- Center petal -->
            <circle cx="37.5" cy="25" r="4" fill="#FFB6D9"/>
            <!-- Surrounding petals -->
            <circle cx="25" cy="37.5" r="4" fill="#FFC9E3"/>
            <circle cx="50" cy="37.5" r="4" fill="#FFC9E3"/>
            <circle cx="32" cy="50" r="4" fill="#FFD6EA"/>
            <circle cx="43" cy="50" r="4" fill="#FFD6EA"/>
            <!-- Center -->
            <circle cx="37.5" cy="37.5" r="5" fill="#FFE0F0"/>
        </g>
        <!-- Leaves -->
        <ellipse cx="20" cy="55" rx="2" ry="6" fill="#7CB342" opacity="0.6" transform="rotate(-40 20 55)"/>
        <ellipse cx="55" cy="60" rx="2" ry="6" fill="#7CB342" opacity="0.6" transform="rotate(40 55 60)"/>
    </svg>

    {{-- Top Center Accent --}}
    <svg class="wedding-decoration top-center leaf-decoration" style="width: 60px; height: 60px; top: 2rem;" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        <!-- Small rose cluster -->
        <circle cx="15" cy="20" r="5" fill="#E8A89C" opacity="0.7"/>
        <circle cx="30" cy="15" r="6" fill="#D4897B" opacity="0.8"/>
        <circle cx="45" cy="20" r="5" fill="#E8A89C" opacity="0.7"/>
        <circle cx="22" cy="35" r="4" fill="#F0B8AC" opacity="0.6"/>
        <circle cx="38" cy="35" r="4" fill="#F0B8AC" opacity="0.6"/>
        <circle cx="30" cy="50" r="3" fill="#8B7355" opacity="0.5"/>
    </svg>

    {{-- Middle Left Accent --}}
    <svg class="wedding-decoration float-up" style="left: 3%; top: 50%; width: 50px; height: 50px; margin-top: -25px;" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
        <!-- Daisy cluster -->
        <g opacity="0.6">
            <circle cx="25" cy="10" r="2.5" fill="#FFD700"/>
            <circle cx="35" cy="15" r="2.5" fill="#FFC700"/>
            <circle cx="38" cy="25" r="2.5" fill="#FFD700"/>
            <circle cx="35" cy="35" r="2.5" fill="#FFC700"/>
            <circle cx="25" cy="40" r="2.5" fill="#FFD700"/>
            <circle cx="15" cy="35" r="2.5" fill="#FFC700"/>
            <circle cx="12" cy="25" r="2.5" fill="#FFD700"/>
            <circle cx="15" cy="15" r="2.5" fill="#FFC700"/>
            <circle cx="25" cy="25" r="3" fill="#FFE700"/>
        </g>
    </svg>

    {{-- Middle Right Accent --}}
    <svg class="wedding-decoration float-up" style="right: 3%; top: 50%; width: 50px; height: 50px; margin-top: -25px; animation-delay: 1s;" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
        <!-- Leaf spray -->
        <g opacity="0.6">
            <path d="M 25 5 Q 30 15 25 30" stroke="#90C590" stroke-width="2" fill="none"/>
            <path d="M 25 30 Q 20 15 25 5" stroke="#A8D5A8" stroke-width="2" fill="none"/>
            <ellipse cx="20" cy="18" rx="2" ry="4" fill="#90C590" opacity="0.7" transform="rotate(-30 20 18)"/>
            <ellipse cx="30" cy="18" rx="2" ry="4" fill="#90C590" opacity="0.7" transform="rotate(30 30 18)"/>
            <ellipse cx="18" cy="25" rx="2" ry="4" fill="#A8D5A8" opacity="0.6" transform="rotate(-45 18 25)"/>
            <ellipse cx="32" cy="25" rx="2" ry="4" fill="#A8D5A8" opacity="0.6" transform="rotate(45 32 25)"/>
        </g>
    </svg>
@endif

{{-- Header decorations (subtle) --}}
<svg class="wedding-decoration" style="position: absolute; top: -10px; right: 50px; width: 40px; height: 40px; opacity: 0.3; z-index: 0;" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
    <circle cx="10" cy="10" r="2" fill="#D4AF37"/>
    <circle cx="20" cy="8" r="2" fill="#D4AF37"/>
    <circle cx="30" cy="10" r="2" fill="#D4AF37"/>
    <circle cx="15" cy="18" r="2" fill="#D4AF37"/>
    <circle cx="25" cy="18" r="2" fill="#D4AF37"/>
</svg>
