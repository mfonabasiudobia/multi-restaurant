<template>
  <div class="bg-gray-50">
    <div class="margin-container py-16">
      <div class="cards-grid">
        <Card
          v-for="card in localizedCards"
          :key="card.id"
          :icon="card.icon"
          :title="card.title"
          :description="card.description"
          class="card-item text-center"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import Card from './Card.vue'
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const cardsData = {
  ro: [
    {
      id: 1,
      icon: 'shirt',
      title: 'MII DE PRODUSE NOI ÎN FIECARE ZI',
      description: 'În fiecare zi adăugăm saci de haine second-hand la kilogram, cu selecții variate de branduri precum Zara, H&M, Nike și multe altele!'
    },
    {
      id: 2,
      icon: 'ad',
      title: 'PREȚURI MICI, HAINE DE CALITATE',
      description: 'Cumpără haine la kg la prețuri cu până la 90% mai mici față de cele din magazine. Stil accesibil pentru toată lumea!'
    },
    {
      id: 3,
      icon: 'ico',
      title: 'CUMPĂRĂ SACI LA KG UȘOR ȘI RAPID',
      description: 'Alege categoria dorită, comandă rapid și primește haine second-hand de calitate direct la tine!'
    },
    {
      id: 4,
      icon: 'truck',
      title: 'LIVRARE RAPIDĂ ÎN TOATĂ ȚARA',
      description: 'Expediem comenzile în doar 1-3 zile lucrătoare, oriunde în România. Ai întrebări? Suntem aici pentru tine!'
    }
  ],
  en: [
    {
      id: 1,
      icon: 'shirt',
      title: 'THOUSANDS OF NEW PRODUCTS EVERY DAY',
      description: 'Every day we add second-hand clothing bags by the kilogram, with varied selections of brands like Zara, H&M, Nike, and many more!'
    },
    {
      id: 2,
      icon: 'ad',
      title: 'LOW PRICES, QUALITY CLOTHES',
      description: 'Buy clothes by the kg at prices up to 90% lower than retail stores. Affordable style for everyone!'
    },
    {
      id: 3,
      icon: 'ico',
      title: 'BUY BAGS BY KG EASILY AND QUICKLY',
      description: 'Choose your desired category, order quickly, and receive quality second-hand clothes directly to you!'
    },
    {
      id: 4,
      icon: 'truck',
      title: 'FAST DELIVERY NATIONWIDE',
      description: 'We ship orders in just 1-3 business days, anywhere in Romania. Have questions? We\'re here for you!'
    }
  ]
};

const localizedCards = computed(() => {
  return cardsData[locale.value] || cardsData.en;
});
</script>

<style scoped>
.cards-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  max-width: 100%;
}

.card-item {
  transition: transform 0.3s ease;
}

.card-item:hover {
  transform: translateY(-5px);
}

/* Responsive breakpoints */
@media (max-width: 1200px) {
  .cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .cards-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
</style>
