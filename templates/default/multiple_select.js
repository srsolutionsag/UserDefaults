SrMultipleSelect = {

    configs: [],

    addConfig: function(config, id) {
        this.configs[id] = config;
    },

    init: function(id, config) {
        console.log('init ' + id);
        var id_cut = id.substring(0, id.indexOf('\\['));
        if (typeof config !== 'undefined') {
            this.addConfig(config, id_cut);
        }

        config = this.configs[id_cut];
        var link = config.ajax_link;
        var replacer = new RegExp('amp;', 'g');
        link = link.replace(replacer, '');
        var settings = {
            placeholder: config.placeholder,
            minimumInputLength: config.minimum_input_length,
            multiple: config.multiple
        };
        if(link.length > 0){
            settings["ajax"] = {
                url: link,
                dataType: 'json',
                data: function(term, page){
                    return {
                        term: term,
                        container_type: config.container_type,
                        page_limit: 10
                    }
                },
                results: function(data, page){
                    return {
                        results: data
                    }
                }
            }

        }

        $("#" + id).select2(settings);
        $("#" + id).select2('data', config.preload);
    },
};