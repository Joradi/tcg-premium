<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-4 py-2.5 text-xs font-bold uppercase tracking-[0.16em] text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition-all duration-200 hover:bg-[#5A189A] focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/50 focus:ring-offset-2 focus:ring-offset-[#2B2D42] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60'
    ]) }}
>
    {{ $slot }}
</button>
