export default class FluxEcoUiSearchInputElement extends HTMLElement {


    #shadowRoot = null;

    /**
     *
     * @type {{fetchedData: {id: string, value: string}|null, styleSheet:  CSSStyleSheet|null, inputContainerElement: HTMLElement|null, labelElement:  HTMLElement|null, searchInputElement:  HTMLElement|null, hiddenInputElement:  HTMLElement|null, searchResultElements:  HTMLElement|null, selectedItemsElement:  HTMLElement|null, dataSrc: string|null}}
     */
    #state = {
        fetchedData: null,
        selectedItemsElement: null,
        labelElement: null,
        inputContainerElement: null,
        searchInputElement: null,
        hiddenInputElement: null,
        searchResultElements: null,
        styleSheet: null,
        dataSrc: null
    };

    constructor() {
        super();
        this.name = "test";
        this.#shadowRoot = this.attachShadow({mode: "closed"})
    }

    /**
     * @param {{name: string, selectedIds: array, dataSrc: string, styleSheet: CSSStyleSheet}} params
     * @return {FluxEcoUiSearchInputElement}
     */
    static create(params) {


        return new this()
            .#defineDataSrc({dataSrc: params.dataSrc})
            .#defineStyleSheet({styleSheet: params.styleSheet})
            .#createInputContainerElement()
            .#createSelectedItemsElement()
            .#createSearchInputElement({dataSrc: params.dataSrc})
            .#createHiddenInputElement({name: params.name})
            .#createSearchResultElements()
            .#fetchData({selectedIds: params.selectedIds})
    }

    render(container) {

        //this.#shadowRoot.adoptedStyleSheets.push(this.#state.styleSheet);
        //because of firefox
        const sheet = new CSSStyleSheet();
        sheet.replaceSync(`
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
            `)
        this.#shadowRoot.adoptedStyleSheets = [sheet];

        container.appendChild(this)
        container.appendChild(this.#state.hiddenInputElement)
    }

    /**
     * @param {{dataSrc: string}} params
     * @return {FluxEcoUiSearchInputElement}
     */
    #defineDataSrc(params) {
        return this.#applyStateChanged({valueName: "dataSrc", value: params.dataSrc});
    }

    /**
     * @param {{styleSheet: {CSSStyleSheet}}} params
     * @return {FluxEcoUiSearchInputElement}
     */
    #defineStyleSheet(params) {
        return this.#applyStateChanged({valueName: "styleSheet", value: params.styleSheet});
    }


    #searchResultElementSelected(params) {
        const itemElement = document.createElement("span");
        itemElement.setAttribute("name", params.id);
        itemElement.setAttribute("class", "selectedSearchResultElement");
        itemElement.innerText = params.value;

        itemElement.addEventListener("click", () => {
            this.#selectedSearchResultElementRemoved(params)
        });

        this.#state.selectedItemsElement.appendChild(itemElement);
        let hiddenInputValues = this.#state.hiddenInputElement.value.split(',')
        hiddenInputValues.push(Number(params.id));
        hiddenInputValues = hiddenInputValues.filter(function (item) {
            return item !== ""
        })

        this.#state.hiddenInputElement.setAttribute("value", hiddenInputValues.join());


        if (this.#state.searchResultElements.children.length > 0) {
            this.#state.searchResultElements.children.namedItem(params.id).remove();
        }

        return this;
    }

    #createInputContainerElement() {
        const name = "inputContainerElement";
        const inputContainerElement = document.createElement("span");
        inputContainerElement.setAttribute("class", name);
        inputContainerElement.setAttribute("name", name);

        this.#applyStateChanged({valueName: name, value: inputContainerElement});
        this.#shadowRoot.appendChild(this.#state.inputContainerElement);
        return this;
    }

    /**
     *
     * @param {{dataSrc: string}} params
     * @return {FluxEcoUiSearchInputElement}
     */
    #createSearchInputElement(params) {
        //const searchInputElement = document.createElement("span");
        const searchInputElement = document.createElement("input");
        //searchInputElement.appendChild(input);
        searchInputElement.setAttribute("name", "searchInputElement");
        searchInputElement.setAttribute("class", "searchInputElement");
        searchInputElement.setAttribute("placeholder", "search and select");

        searchInputElement.addEventListener("focus", () => {
            this.#fetchData(null);
        });
        searchInputElement.addEventListener('input', (event) => {
            this.#filterSearchResultElementsBySearchInput();
        });
        this.#applyStateChanged({valueName: "searchInputElement", value: searchInputElement});
        this.#state.inputContainerElement.appendChild(this.#state.searchInputElement);
        return this;
    }

    #changeSearchResultElements(dataItems) {
        //todo
        this.#state.searchResultElements.innerHTML = '';
        dataItems.forEach((item) => {
            this.#appendSearchResultElement(item)
        });
    }

    #appendSearchResultElement(params) {
        let childNodes = Array.from(this.#state.searchResultElements.children);

        const searchResultElement = document.createElement("span");
        searchResultElement.setAttribute("class", "searchResultElement");
        searchResultElement.setAttribute("name", params.id);
        searchResultElement.innerText = params.value;
        searchResultElement.addEventListener("click", () => {
            this.#searchResultElementSelected({id: params.id, value: params.value})
            //todo
            this.#state.searchResultElements.setAttribute("style", "display: none");
        });
        if (this.#state.selectedItemsElement.children.namedItem(params.id) === null
        ) {
            childNodes.push(searchResultElement)
        }
        childNodes.sort(this.#sortElementsByNameSorter)
        this.#state.searchResultElements.replaceChildren(...childNodes);
    }

    #sortElementsByNameSorter(a, b) {
        return a.innerText.localeCompare(b.innerText);
    }


    /**
     * @param {{selectedIds: array}|null} params
     */
    #fetchData(params) {

        const fetchOptions = {
            cache: 'default',
            headers: {
                "Content-Type": "application/json",
            },
            method: 'GET',
            mode: 'cors'
        };
        fetch(
            this.#state.dataSrc,
            fetchOptions
        ).then(response => {
            if (response.ok) {
                return response.json();
            }
        })
            .then(data => {
                let fetchDataItems = [];
                let selectedDataItems = [];

                if (data.length > 0) {
                    data.forEach((item) => {

                        let searchResultElement = {id: item.id, value: item.title};
                        fetchDataItems.push(searchResultElement);
                        if (params !== null) {
                            if (params.selectedIds.includes(searchResultElement.id)) {
                                selectedDataItems.push(searchResultElement)
                            }
                        }
                    });
                    this.#applyStateChanged({valueName: "fetchedData", value: fetchDataItems});

                    if (params === null) {
                        this.#changeSearchResultElements(fetchDataItems)
                        //todo
                        this.#state.searchResultElements.setAttribute("style", "");

                    }

                    selectedDataItems.forEach((searchResultElement) => {
                        this.#searchResultElementSelected(searchResultElement)
                    });
                }
                if (params === null) {
                    if (this.#state.searchResultElements.innerHTML === "") {
                        this.#state.searchResultElements.innerHTML = 'no values found';
                    }
                }
            })


        return this;
    }

    #createSearchResultElements() {
        const searchResultElements = document.createElement("div");
        searchResultElements.setAttribute("name", "searchResultElements");
        searchResultElements.setAttribute("class", "searchResultElements");
        this.#applyStateChanged({valueName: "searchResultElements", value: searchResultElements});
        this.#state.inputContainerElement.appendChild(this.#state.searchResultElements);
        return this;
    }


    #createSelectedItemsElement() {
        const selectedItemsElement = document.createElement("div");
        selectedItemsElement.setAttribute("name", "selectedItemsElement");
        this.#applyStateChanged({valueName: "selectedItemsElement", value: selectedItemsElement});
        this.#state.inputContainerElement.appendChild(this.#state.selectedItemsElement);
        return this;
    }

    /**
     *
     * @param {{name: string}} params
     * @return {FluxEcoUiSearchInputElement}
     */
    #createHiddenInputElement(params) {
        const hiddenInputElement = document.createElement("input");
        hiddenInputElement.setAttribute("name", "hiddenInputElement");
        hiddenInputElement.type = "hidden";
        hiddenInputElement.name = params.name;
        hiddenInputElement.setAttribute("value", "");
        this.#applyStateChanged({valueName: "hiddenInputElement", value: hiddenInputElement});
        return this;
    }

    #filterSearchResultElementsBySearchInput() {
        let fetchedData = this.#state.fetchedData;
        let value = this.#state.searchInputElement.value;

        fetchedData = fetchedData.filter((item) => {
            return (item.value.toLowerCase().includes(value.toLowerCase()))
        })

        this.#changeSearchResultElements(fetchedData)
    }

    #selectedSearchResultElementRemoved(params) {
        if (this.#state.selectedItemsElement.children.namedItem(params.id) !== null) {
            this.#state.selectedItemsElement.children.namedItem(params.id).remove();
        }
        let hiddenInputValues = this.#state.hiddenInputElement.value.split(',').map(Number);
        hiddenInputValues = hiddenInputValues.filter(function (item) {
            return item !== params.id
        })
        this.#state.hiddenInputElement.setAttribute("value", hiddenInputValues.join());

        this.#filterSearchResultElementsBySearchInput();

        //this.#appendSearchResultElement(params);
        return this;
    }

    /**
     * @param {{valueName: string, value: any}} stateChanged
     * @return {FluxEcoUiSearchInputElement}
     */
    #applyStateChanged(stateChanged) {
        this.#state[stateChanged.valueName] = stateChanged.value
        return this;
    }
}