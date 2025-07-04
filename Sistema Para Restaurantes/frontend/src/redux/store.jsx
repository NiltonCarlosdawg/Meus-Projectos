import { configureStore } from "@reduxjs/toolkit";
import booleanReducer from "./booleanSlice"

export default configureStore({
  reducer:{
    boolean:booleanReducer
  }
})