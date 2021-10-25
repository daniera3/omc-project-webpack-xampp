import React from "react";
import ReactDOM from "react-dom";
import AppRouter from "./AppRouter";


if (module.hot) module.hot.accept()

ReactDOM.render(<AppRouter />, document.querySelector("#root"))