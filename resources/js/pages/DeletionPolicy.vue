<template>
    <div class="main-container">
         <div class="max-w-7xl mx-auto py-6 md:py-8 lg:py-12">
            <div class="text-slate-950 text-lg sm:text-xl lg:text-3xl font-bold mb-2">
                {{ $t("Solicitare ștergere cont – Second Hub") }}?
            </div>

            <div class="text-slate-950 text-base sm:text-lg font-normal tracking-tight mb-7">
               Dacă dorești să îți ștergi contul din aplicația <strong>Second Hub</strong>, completează formularul de mai jos. Cererea ta va fi procesată în cel mult 7 zile lucrătoare.
            </div>

     

        <form action="mailto:suport@secondhub.ro" method="post" enctype="text/plain" class="space-y-5">
              <div>
                <label for="name" class="form-label mb-2">Nume complet:</label>
                <input type="text" id="name" name="Nume" class="form-input" required>
            </div>

            <div>
                <label for="email" class="form-label mb-2">Adresa de e-mail folosită în aplicație:</label>
                <input type="email" id="email" name="Email" class="form-input"  required>
            </div>

            <div>
                <label for="reason" class="form-label mb-2">Motivul ștergerii (opțional):</label>
                <textarea id="reason" name="Motiv" rows="4" class="form-input" ></textarea>
            </div>

            <button type="submit"   class="bg-primary text-white px-6 py-4 rounded-lg">Trimite solicitarea</button>
        </form>

        <p style="margin-top: 2rem;">
            Pentru întrebări suplimentare, ne poți contacta direct la:<br>
            📧 <a href="mailto:suport@secondhub.ro">suport@secondhub.ro</a><br>
            📞 <a href="tel:+40755511123">+40 755 511 123</a>
        </p>

        </div>

        <InfoCards />
        <BecomeSellerSection />
        <AppDownloadPopup />
    </div>
</template>


<style scoped>
.form-label {
    @apply text-slate-700 text-base font-normal block mb-1;
}

.form-input {
    @apply p-3 rounded-lg border focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}
</style>

