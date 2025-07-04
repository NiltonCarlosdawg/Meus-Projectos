import { createSlice } from "@reduxjs/toolkit";

export const CoinSlice = createSlice({
  name:"coin",
  initialState:{
    coin:"USDT"
  },
  reducers:{
    setCoin(state, {payload}){
      state.coin = payload
    }
  }
})

export const {setCoin} = CoinSlice.actions
export default CoinSlice.reducer