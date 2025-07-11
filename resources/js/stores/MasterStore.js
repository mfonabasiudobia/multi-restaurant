import { defineStore } from "pinia";

export const useMaster = defineStore("masterStore", {
    state: () => ({
        locale: localStorage.getItem('locale') || 'ro',
        logo: null,
        currency: "$",
        position: "prefix",
        appName: "",
        showDownloadApp: false,
        playStoreLink: null,
        appStoreLink: null,
        multiVendor: true,
        paymentGateways: [],
        mobile: null,
        email: null,
        showFooter: true,
        address: null,
        footerText: null,
        footerDescription: null,
        footerLogo: null,
        footerQr: null,
        socialLinks: [],
        basketCanvas: false,
        search: null,
        categories: [],
        themeColors: {
            primary: null,
            primary50: null,
            primary100: null,
            primary200: null,
            primary300: null,
            primary400: null,
            primary500: null,
            primary600: null,
            primary700: null,
            primary800: null,
            primary900: null,
            primary950: null,
        },
        pusher_app_key: null,
        pusher_app_cluster: null,
        app_environment: "local",
        register_otp_verify: false,
        register_otp_type: null,
        forgot_otp_type: null,
        languages: [],
        socialAuths: [],
        seasons:[],
        qualities:[],
        sizes: [],
        colors: [],
        filter_categories: {
            qualities: { 
                title: 'Calitate',
                items: [] 
            },
            seasons: { 
                title: 'Anotimpuri',
                items: [] 
            }
        },
        settings: {}
    }),

    getters: {
        getLocale: (state) => state.locale,
        getLogo: (state) => state.logo,
        getCurrency: (state) => state.currency,
        getPosition: (state) => state.position,
        getAppName: (state) => state.appName,
        getShowDownloadApp: (state) => state.showDownloadApp,
        getPlayStoreLink: (state) => state.playStoreLink,
        getAppStoreLink: (state) => state.appStoreLink,
        getMultiVendor: (state) => state.multiVendor,
        getPaymentGateways: (state) => state.paymentGateways,
        getMobile: (state) => state.mobile,
        getEmail: (state) => state.email,
        getShowFooter: (state) => state.showFooter,
        getAddress: (state) => state.address,
        getFooterText: (state) => state.footerText,
        getFooterDescription: (state) => state.footerDescription,
        getFooterLogo: (state) => state.footerLogo,
        getFooterQr: (state) => state.footerQr,
        getSocialLinks: (state) => state.socialLinks,
        getBasketCanvas: (state) => state.basketCanvas,
        getSearch: (state) => state.search,
        getCategories: (state) => state.categories,
        getThemeColors: (state) => state.themeColors,
        getPusherAppKey: (state) => state.pusher_app_key,
        getPusherAppCluster: (state) => state.pusher_app_cluster,
        getAppEnvironment: (state) => state.app_environment,
        getRegisterOtpVerify: (state) => state.register_otp_verify,
        getRegisterOtpType: (state) => state.register_otp_type,
        getForgotOtpType: (state) => state.forgot_otp_type,
        getLanguages: (state) => state.languages,
        getSocialAuths: (state) => state.socialAuths,
        getSeasons: (state) => state.seasons,
        getQualities: (state) => state.qualities,
        getSizes: (state) => state.sizes,
        getColors: (state) => state.colors,
        getFilterCategories: (state) => state.filter_categories,
        getSettings: (state) => state.settings
    },

    actions: {
        setLocale(locale) {
            this.locale = locale;
            localStorage.setItem('locale', locale);
        },
        initializeLocale() {
            if (!localStorage.getItem('locale')) {
                this.setLocale('ro');
            }
        },
        setBasketCanvas(basketCanvas) {
            this.basketCanvas = basketCanvas;
        },
        setSearch(search) {
            this.search = search;
        },
        fetchData() {
            console.log("Fetching master data...");
            return new Promise((resolve, reject) => {
                axios.get("/master").then((response) => {
                    console.log("Master data response:", response.data);
                    
                    this.currency = response.data.data.currency.symbol;
                    this.position = response.data.data.currency.position;
                    this.appName = response.data.data.app_name;
                    this.playStoreLink = response.data.data.google_playstore_link;
                    this.appStoreLink = response.data.data.app_store_link;
                    this.multiVendor = response.data.data.multi_vendor;
                    this.mobile = response.data.data.mobile;
                    this.email = response.data.data.email;
                    this.showFooter = response.data.data.web_show_footer;
                    this.address = response.data.data.address;
                    this.paymentGateways = response.data.data.payment_gateways;
                    this.footerText = response.data.data.web_footer_text;
                    this.footerDescription =
                        response.data.data.web_footer_description;
                    this.footerLogo = response.data.data.web_footer_logo;
                    this.footerQr = response.data.data.footer_qr;
                    this.logo = response.data.data.web_logo;
                    this.socialLinks = response.data.data.social_links;
                    this.themeColors = response.data.data.theme_colors;
                    this.pusher_app_key = response.data.data.pusher_app_key;
                    this.pusher_app_cluster = response.data.data.pusher_app_cluster;
                    this.app_environment = response.data.data.app_environment;
                    this.showDownloadApp = response.data.data.show_download_app;
                    this.register_otp_verify =
                        response.data.data.register_otp_verify;
                    this.register_otp_type = response.data.data.register_otp_type;
                    this.forgot_otp_type = response.data.data.forgot_otp_type;
                    this.languages = response.data.data.languages;
                    this.socialAuths = response.data.data.social_auths;
                    this.seasons = response.data.data.seasons || [];
                    this.qualities = response.data.data.qualities || [];
                    this.colors = response.data.data.colors || [];
                    this.sizes = response.data.data.sizes || [];
                    
                    // Handle filter_categories specifically
                    if (response.data.data.filter_categories) {
                        console.log("Setting filter_categories:", response.data.data.filter_categories);
                        this.filter_categories = response.data.data.filter_categories;
                    } else {
                        console.log("No filter_categories in response, using default");
                        this.filter_categories = {
                            qualities: { title: 'Calitate', items: [] },
                            seasons: { title: 'Anotimpuri', items: [] }
                        };
                    }
                    
                    console.log("Master data fetched successfully");
                    console.log("Filter categories:", this.filter_categories);
                    console.log("Qualities:", this.qualities);
                    console.log("Seasons:", this.seasons);
                    
                    resolve(response.data);
                }).catch(error => {
                    console.error("Error fetching master data:", error);
                    reject(error);
                });
            });
        },
        async fetchSettings() {
            try {
                const response = await axios.get('/api/settings');
                this.settings = response.data.data;
            } catch (error) {
                console.error('Error fetching settings:', error);
            }
        },
        showCurrency(amount) {
            if (this.position === "prefix") {
                return this.currency + amount;
            } else {
                return amount + this.currency;
            }
        },
    },

    persist: true,
});
