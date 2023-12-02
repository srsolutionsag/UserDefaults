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
            const searchStyleSheet = `
        :host {

            }
        .inputContainerElement {
                margin: 0;
                padding: 10px;
                display: block;
                position: relative;
                border: 1px solid #aaa;
                cursor: text;
                background-color: #fff;
            }
        .selectedSearchResultElement {
                padding: 5px;
                margin-right: 5px;
                cursor: pointer;
                color: #fff;
                background-color: #4c6586;
            }
        .selectedSearchResultElement:before{
                padding-right: 5px;
                display: inline-block;
         
            }
        .searchInputElement {
                margin-top: 10px;
                height: 25px;
            }

        .searchResultElements {
                border-left: 1px solid  #aaa;
                box-shadow: 0 2px 3px 0 rgba(34,36,38,.15);
                overflow-x: hidden;
                overflow-y: auto;
                max-height: 300px;
            }
        .searchResultElement {
                box-sizing: border-box;
                direction: ltr;
                unicode-bidi: embed;
                list-style: none;
                display: list-item;
                cursor: pointer;
                padding: 10px 5px;
            }
        .searchResultElement:hover {
                background: #4c6586;
                color: #fff;
            }
            `


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