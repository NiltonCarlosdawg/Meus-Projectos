import { configureStore } from "@reduxjs/toolkit";
import booleanReducer from "./Bslice"
import Coinreducer from "./CoinSlice"
export default configureStore({
  reducer:{
    boolean:booleanReducer,
    coin:Coinreducer
  }
})