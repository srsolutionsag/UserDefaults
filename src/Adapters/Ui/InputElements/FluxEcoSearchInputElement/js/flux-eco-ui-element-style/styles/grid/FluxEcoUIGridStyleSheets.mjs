import regularGridStyleSheet from './css/regular.css' assert {type: 'css'};

export default class FluxEcoUIGridStyleSheets {

    /**
     * @type {{regularStyleSheet: null|CSSStyleSheet, responsiveStyleSheet: null|CSSStyleSheet}}
     */
    #state = {
        regularStyleSheet: null,
        responsiveStyleSheet: null
    }

    constructor() {
    }

    /**
     * @return {FluxEcoUIGridStyleSheets}
     */
    static create() {
        return new this();
    }

    /**
     * @return {CSSStyleSheet}
     */
    get regularGridStyleSheet() {
        return this.#regularStyleSheetInstance;
    }

    get #regularStyleSheetInstance() {
        if (this.#state.regularStyleSheet === null) {
            this.#applyChangeStateValue({valueName: "regularStyleSheet", value: regularGridStyleSheet});
        }
        return this.#state.regularStyleSheet;
    }

    /**
     * @param {{valueName: string, value: any}} params
     *
     * @return FluxEcoUIGridStyleSheets
     */
    #applyChangeStateValue(params) {
        this.#state[params.valueName] = params.value
        return this;
    }
}