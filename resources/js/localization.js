import { createI18n } from "vue-i18n";
import axios from "axios";

const i18n = createI18n({
    locale: localStorage.getItem("locale") || "ro",
    fallbackLocale: "ro",
    messages: {},
});

async function fetchLocalizationData() {
    const lang = localStorage.getItem("locale") || "ro";
    console.log("Fetching language file", lang);
    
    try {
        // First try to get from frontend translations
        const response = await axios.get(`/translations/${lang}`);
        console.log("Loaded frontend language file", response.data);
        i18n.global.setLocaleMessage(lang, response.data);
        i18n.global.locale = lang;
    } catch (error) {
        console.error("Failed to load frontend language file", error);
        
        // Fallback to backend translations
        try {
            const backupResponse = await axios.get(`/lang/${lang}`);
            console.log("Loaded backend language file", backupResponse.data);
            i18n.global.setLocaleMessage(lang, backupResponse.data);
            i18n.global.locale = lang;
        } catch (backupError) {
            console.error("Failed to load backup language file", backupError);
            // If both fail, set empty translations to prevent errors
            i18n.global.setLocaleMessage(lang, {});
        }
    }
}

// Initialize translations on load
fetchLocalizationData();

export default { i18n, fetchLocalizationData };
