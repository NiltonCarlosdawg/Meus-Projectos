import { PiCoinFill } from "react-icons/pi"
import { aboutCoins, hot, newCoins, topGainers, topvolume } from "./Object"
import { useState } from "react"
import { BiSearch } from "react-icons/bi"
import { FaArrowDown } from "react-icons/fa6"
import AllCryptos from "./AllCryptos"

const Overview = () => {
  const [selectId, setSelectId] = useState(1)
  const [search, setSearch] = useState("")
  return (
    <div>
      <div className="grid grid-cols-4 gap-4 my-[20px]">
        <div className="shadow-xl border-[#80808056] rounded-[4px] ">
          <div className="flex justify-between items-center border-b-[1px] border-b-[#80808056] pb-1 p-2 bg-[#0000001e] rounded-t-[4px]">
            <span>Hot Coins</span>
                   <div className="text-[13px]">More <span className="text-[#888da8]">{">"}</span> </div>
          </div>
          {
            hot.map((item, index)=>{

              return(
                <div key={index} className="flex items-center justify-between px-2 cursor-pointer border-b-[1px] border-[#80808056]">
                  <span className="flex items-center"><PiCoinFill size={20} color="#f5cf34"/>{item.nome}</span>
                  <span className="text-[#888da8] my-[5px]">${item.price}K</span>
                      <span className={item.change > 0 ?"text-[#29a329] font-bold":"text-[#a92323] font-bold"}>{item.change}%</span>
                </div>
              )
            })
          }
        </div>
        <div className="shadow-xl border-[#80808056] rounded-[4px] ">
          <div className="flex justify-between items-center border-b-[1px] border-b-[#80808056] pb-1 p-2 bg-[#0000001e] rounded-t-[4px]">
            <span>New Listings</span>
            <div className="text-[13px]">More <span className="text-[#888da8]">{">"}</span> </div>
          </div>
          {
            newCoins.map((item, index)=>{

              return(
                <div key={index} className="flex items-center justify-between px-2 cursor-pointer border-b-[1px] border-[#80808056]">
                  <span className="flex items-center"><PiCoinFill size={20} color="#f5cf34"/>{item.nome}</span>
                  <span className="text-[#888da8] my-[5px]">${item.price}K</span>
                      <span className={item.change > 0 ?"text-[#29a329] font-bold":"text-[#a92323] font-bold"}>{item.change}%</span>
                </div>
              )
            })
          }
        </div>
        <div className="shadow-xl border-[#80808056] rounded-[4px] ">
          <div className="flex justify-between items-center border-b-[1px] border-b-[#80808056] pb-1 p-2 bg-[#0000001e] rounded-t-[4px]">
            <span>Top Gainer Coin</span>
                   <div className="text-[13px]">More <span className="text-[#888da8]">{">"}</span> </div>
          </div>
          {
            topGainers.map((item, index)=>{

              return(
                <div key={index} className="flex items-center justify-between px-2 cursor-pointer border-b-[1px] border-[#80808056]">
                  <span className="flex items-center"><PiCoinFill size={20} color="#f5cf34"/>{item.nome}</span>
                  <span className="text-[#888da8] my-[5px]">${item.price}K</span>
                      <span className={item.change > 0 ?"text-[#29a329] font-bold":"text-[#a92323] font-bold"}>{item.change}%</span>
                </div>
              )
            })
          }
        </div>
        <div className="shadow-xl border-[#80808056] rounded-[4px] ">
          <div className="flex justify-between items-center border-b-[1px] border-b-[#80808056] pb-1 p-2 bg-[#0000001e] rounded-t-[4px]">
            <span>Top Volume Coin</span>
                   <div className="text-[13px]">More <span className="text-[#888da8]">{">"}</span> </div>
          </div>
          {
            topvolume.map((item, index)=>{

              return(
                <div key={index} className="flex items-center justify-between px-2 cursor-pointer border-b-[1px] border-[#80808056]">
                  <span className="flex items-center"><PiCoinFill size={20} color="#f5cf34"/>{item.nome}</span>
                  <span className="text-[#888da8] my-[5px]">${item.price}K</span>
                  <span className={item.change > 0 ?"text-[#29a329] font-bold":"text-[#a92323] font-bold"}>{item.change}%</span>
                </div>
              )
            })
          }
        </div>
      </div>
      <div className="my-[20px]">
        <div className="flex items-center justify-between">
          <div className="flex gap-5 font-bold">
            {
              aboutCoins.map((item, index)=>{
                const pegar = ()=>{
                  setSelectId(item.id)
                }
                return(
                  <div key={index} onClick={pegar} className={item.id === selectId?" cursor-pointer":"text-[#888da8] cursor-pointer"}>
                    {item.nome}
                  </div>
                )
              })
            }
          </div>
          <div className="flex items-center gap-2 rounded-[5px] border-[1px] border-[#80808056] p-1">
            <BiSearch color="#888da8"/>
            <input type="text" className="bg-[#80808000]" onChange={(e)=>setSearch(e.target.value)}/>
          </div>
        </div>
        <div className="border-[1px] border-[#80808056] rounded-[5px] my-[20px] p-2">
          <h1 className="text-[20px] font-[700]">Top Tokens by Markets Capitalization</h1>
          <p className="text-[#888da8] text-[13px]">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ratione nesciunt adipisci cum sunt, quam provident reprehenderit dicta expedita laudantium debitis, harum enim eos, porro ea aliquam facilis magni saepe esse?</p>
          <p className="flex items-center font-bold text-[14px] cursor-pointer">show more <FaArrowDown size={10} color="#888da8"/></p>
        </div>
        <AllCryptos search={search}/>
      </div>
    </div>
  )
}

export default Overview