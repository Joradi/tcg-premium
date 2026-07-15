@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/70 px-3 py-2.5 text-[#FFF8E7] shadow-sm outline-none transition placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-2 focus:ring-[#80FFDB]/20 disabled:cursor-not-allowed disabled:opacity-60'
    ]) }}
>
