import { useState } from "react"

const SetCurrency = (amount)=>{
  const [newValue, setNewValue] = useState(0)
  const set = (amount)=>{
    if(String(amount).includes(",")){
      amount = Number(amount.replace(",","."))
    }
    const pamount = amount
    const numberOfDecimals = 2
    const formattedValue = pamount.toLocaleString('pt', {
      style:"currency",
      currency:"AOA",
      minimumFractionDigits: numberOfDecimals,
      maximumFractionDigits: numberOfDecimals
    })
    setNewValue(formattedValue)
    return formattedValue
  }
  return [set, newValue]

}

export default SetCurrency