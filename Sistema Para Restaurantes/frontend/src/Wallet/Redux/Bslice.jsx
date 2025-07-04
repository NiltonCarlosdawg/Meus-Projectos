import { createSlice } from "@reduxjs/toolkit";

export const Bslice = createSlice({
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

export const {setboolean, changeboolean} = Bslice.actions
export const selectboolean = state=>state.boolean

export default Bslice.reducer