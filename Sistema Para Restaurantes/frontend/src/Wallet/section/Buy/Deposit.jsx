import { BiClipboard, BiDownArrowAlt, BiEuro, BiSearch } from "react-icons/bi"
import { BsBank, BsCurrencyExchange } from "react-icons/bs"
import { GrCurrency, GrMastercard } from "react-icons/gr"
import { currency, faq, fund } from "./object"
import { FaCcMastercard } from "react-icons/fa6"
import { useEffect, useState } from "react"
import { AiFillEuroCircle } from "react-icons/ai"
import { useSelector } from "react-redux"

const Deposit = () => {
  const [show, setShow] = useState(false)
  const [myindex, setMyIndex] = useState(0)
  const myvalue = useSelector(state=>state.boolean.boolean)
  return (
    <div className="px-[4%]">
      <h1 className="text-[35px] font-[500] font-[var] mb-[20px]">Deposit Fiat</h1>
      <div className="flex justify-between gap-10">
        <div className="basis-[30%]">
          <span className="font-[600] text-[14px] ">Currency</span>
          <div className="border-[1px] border-[#80808056] rounded-[5px] p-2 cursor-pointer flex items-center justify-between" onClick={()=>setShow(!show)}>
                {<>
                  <div className="flex items-center gap-2">
                    <GrCurrency color="#fcd535"/>
                    <span>
                      {currency[myindex].nome}
                    </span>
                  </div>
                  <BiDownArrowAlt/>
                  </>
                }
            </div>
          {show&&<div className="flex items-center relative top-[45%]" >
            <div className={myvalue?"bg-[#fbfbff] w-full rounded-[5px] shadow-xl px-[2%] absolute h-[300px] overflow-y-scroll snap-proximity" :"bg-[#1e2329] w-full rounded-[5px] shadow-xl px-[2%] absolute h-[300px] overflow-y-scroll snap-proximity"}>
              <div className="flex items-center border-[1px] border-[#80808056] my-[10px] px-[2%] py-[5px] rounded-[5px] sticky top-0 backdrop-blur-sm">
                <BiSearch/>
                <input type="text" placeholder="search" className="bg-[#80808000]"/>
              </div>
              <div className="">
                {
                  currency.map((item, index)=>{
                    const catchya = ()=>{
                      setMyIndex(currency.findIndex(items=>items.nome === item.nome))
                      setShow(false)
                    }
                    return(
                      <div key={index} className="flex items-center my-[10px] cursor-pointer gap-2" onClick={catchya}>
                        {<><BsCurrencyExchange color="#fcd535" size={20}/> <div>
                          <p>{item.nome}</p>
                          <p className="text-[#888da8]">{item.country}</p> 
                        </div></>}
                      </div>
                    )
                  })
                }
              </div>
            </div>
          </div>}
          <div className="my-[20px]">
            <p className="font-[600] text-[14px]">Recommended</p>
            <div className="border-[2px] rounded-[5px] px-2 py-[10px]">
              <div className="flex items-center font-[500]">
                <FaCcMastercard color="#fcd535"/>
                Card(VISA/Mastercard)
              </div>
              <p className="text-[#888da8] text-[13px] px-[5%]">2% Fee, Instant arrival</p>
            </div>

            <div className="my-[20px]">
              <p className="font-[600] text-[14px]">Others</p>
              <div className="border-[1px] border-[#80808056] rounded-[5px] p-1">
                <div className="flex items-center">
                  <BsBank/>
                  <span>Bank transfer(SEPA)</span>
                </div>
                <p className="text-[#888da8] text-[13px] px-[5%]">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Exercitationem magni architecto</p>
              </div>
            </div>
            <button className=" bg-[#fcd535] p-1 rounded-[4px] text-[black]">Log in /SiGN Up</button>
          </div>
        </div>
        <div>
          <div className="flex justify-between">FAQ <button className="text-[#888da8] text-[13px]">View More {">"}</button></div>
          <div>
            {
              faq.map((item, index)=>{
                return(
                  <div key={index} className="flex items-center justify-between gap-2 mb-[20px] cursor-pointer">
                    <span className="border-[1px] border-[#80808056] px-2 rounded-[5px]">{item.id}</span>
                    <span>{item.nome}</span>
                    <span><BiClipboard/></span>
                  </div>
                )
              })
            }
          </div>
        </div>
      </div>
      <div>
        <h1 className="text-[30px] font-[500] font-[var] my-[40px]">Fund Your Account with Fiat Currencies</h1>
        <div className="grid grid-cols-3 gap-4">
          {
            fund.map((item, index)=>{

              return(
                <div key={index} className="border-[1px] border-[#80808056] rounded-[5px]">
                  <img src="tut.png" alt="" className="rounded-t-[5px]"/>
                  <div className="text-center px-[10%]">
                    <h1 className="text-[20px] font-bold text-center my-[10px]">{item.nome}</h1>
                    <p className="text-[13px] text-[#888da8]">{item.info}</p>
                  </div>
                </div>
              )
            })
          }
        </div>
      </div>
      <div className="flex items-center my-[80px] px-[2%]">
        <div className="pr-[120px]">
          <h1 className="text-[30px] font-[var] font-[500] mb-[10px]">Why Deposit EUR with SEPA Transfer?</h1>
          <p className="text-[#888da8] text-[14px]">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nam impedit nemo obcaecati deleniti ratione! Accusamus id repellendus voluptas assumenda explicabo eaque ab velit voluptates. Quibusdam ratione fugiat perferendis sint ea!</p>
        </div>
        <img src="deposit.png" alt="" className="h-full w-[50%]"/>
      </div>
    </div>
  )
}

export default Deposit