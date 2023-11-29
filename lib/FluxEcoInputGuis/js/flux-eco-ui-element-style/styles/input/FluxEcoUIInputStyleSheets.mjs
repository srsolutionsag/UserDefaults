import searchStyleSheet from './css/search.css' assert {type: 'css'};

export default class FluxEcoUIInputStyleSheets {

    /**
     * @type {{searchInputStyleSheet: null|CSSStyleSheet}}
     */
    #state = {
        searchInputStyleSheet: null,
    }

    constructor() {
    }

    /**
     * @return {FluxEcoUIInputStyleSheets}
     */
    static create() {
        return new this();
    }

    /**
     * @return {CSSStyleSheet}
     */
    get searchInputStyleSheet() {
        return this.#searchInputStyleSheet;
    }

    get #searchInputStyleSheet() {
        if (this.#state.searchInputStyleSheet === null) {
            this.#applyChangeStateValue({valueName: "searchInputStyleSheet", value: searchStyleSheet});
        }
        return this.#state.searchInputStyleSheet;
    }

    /**
     * @param {{valueName: string, value: any}} params
     *
     * @return FluxEcoUIInputStyleSheets
     */
    #applyChangeStateValue(params) {
        this.#state[params.valueName] = params.value
        return this;
    }
}