import { useState } from "react"
import { buy, comprar } from "./object"
import LIstCry from "../../forall/LIstCry"
import { BiCoin } from "react-icons/bi"
import { PiCoinVertical } from "react-icons/pi"
import Howbuy from "./Howbuy"
import Tether from "./Tether"
import Questions from "../Questions"
import Deposit from "./Deposit"
import Recurring from "./Recurring"
const Buy = () => {
  const [selectId, setSelectId] = useState(0)
  const [selectinput, setSelectInput] = useState(0)
  const [Bit, setBit] = useState("USDT")
  return (
    <div className=" ">
      <div className="flex justify-between items-center border-b-[1px] border-b-[#80808056] pb-[0px] px-[4%]">
        <div className="flex items-center  mt-[20px] gap-4 ">
          {
            buy.map((item, index)=>{
              const pegar =()=>{
                setSelectId(item.id)
              }
              return(
                <div onClick={pegar} key={index} className={item.id === selectId?"border-b-[4px] border-b-[#f0b90b] cursor-pointer pb-[10px]":"cursor-pointer pb-[10px]"}>
                  {item.nome}
                </div>
              )
            })
          }
        </div>
      </div>
      {selectId === 0?
        <>
        <div className="flex justify-between my-[20px] items-center px-[4%]">
        <div>
          <h1 className="text-[35px] font-[600] font-[var] mb-[20px]">Buy Crypto</h1>
          <LIstCry setBit={setBit}/>
        </div>
        <div className="border-[1px] border-[#80808056] rounded-[4px] w-[30%] h-[400px]">
          <div className="flex justify-between text-[16px] font-bold text-center mb-[10px]">
            {
              comprar.map((item, index)=>{
                const pegar = ()=>{
                  setSelectInput(item.id)
                }
                return(
                  <div className={item.id === selectinput?"p-3 cursor-pointer w-full ":"p-3 cursor-pointer w-full bg-[#41445191] text-[#888da8]"} onClick={pegar} key={index}>
                    {item.nome}
                  </div>
                )
              })
            }
          </div>
          {selectinput === 0?<div className="px-[2%]">
            <div className="w-full border-[1px] border-[#80808056] rounded-[4px] py-1 flex items-end justify-between p-3 mb-[10px]">
              <div >
                <p className="text-[#888da8]">Spend</p>
                <input placeholder="1.04" type="number" className="bg-[#80808000]" />
              </div>
              <span className="font-bold text-[13px]">$USD</span>
            </div>
            <div className="w-full border-[1px] border-[#80808056] rounded-[4px] py-1 flex items-end justify-between px-3">
              <div >
                <p className="text-[#888da8]">Received</p>
                <input placeholder="1" type="number" className="bg-[#80808000]"/>
              </div>
              <span className="font-bold text-[13px] flex items-center"><PiCoinVertical color="#f8d33a"/>{Bit}</span>
            </div>
          </div>:<div className="px-[2%]">
          <div className="w-full border-[1px] border-[#80808056] rounded-[4px] py-1 flex items-end justify-between px-3 mb-[10px]">
              <div >
                <p className="text-[#888da8]">Received</p>
                <input placeholder="1" type="number" className="bg-[#80808000]"/>
              </div>
              <span className="font-bold text-[13px] flex items-center"><BiCoin color="#f8d33a"/>{Bit}</span>
            </div>
            <div className="w-full border-[1px] border-[#80808056] rounded-[4px] py-1 flex items-end justify-between p-3 mb-[10px]">
              <div >
                <p className="text-[#888da8]">Spend</p>
                <input placeholder="Enter amount" type="number" className="bg-[#80808000]" />
              </div>
              <span className="font-bold text-[13px]">$USD</span>
            </div>

          </div>}
        </div>
    
      </div>
               <Howbuy/>
               <Tether/>
               <Questions/></>:selectId === 2?
               <Deposit/>:<Recurring/>
      }
    </div>
  )
}

export default Buy