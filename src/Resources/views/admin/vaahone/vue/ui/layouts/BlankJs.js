import globalComponents from "../vaahnuxt/helpers/globalComponents";

export default {
    computed:{
        root() {return this.$store.getters['root/state']},
    },
    components:{
        ...globalComponents,

    },
    data()
    {
        let obj = {
            assets: null,
        };

        return obj;
    },
    watch: {



    },
    mounted() {
        //---------------------------------------------------------------------

    },
    methods: {
        //---------------------------------------------------------------------

        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
    }
}
