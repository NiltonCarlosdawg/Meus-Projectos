import { createSlice } from "@reduxjs/toolkit";

export const booleanSlice = createSlice({
  name:"boolean",
  initialState:{
    boolean:false
  },
  reducers:{
    setboolean(state, {payload}){
      return{...state, boolean:true}
    },
    changeboolean(state, {payload}){
      return{...state, boolean:false}
    }
  },
})

export const {setboolean, changeboolean} = booleanSlice.actions
export const selectboolean = state=>state.boolean

export default booleanSlice.reducer