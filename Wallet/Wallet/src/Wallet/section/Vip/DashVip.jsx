import { BiDollarCircle, BiDonateHeart, BiGift, BiPlus } from "react-icons/bi"
import { faq, Taxas, vip } from "./object"
import { VscTasklist } from "react-icons/vsc"
import { useState } from "react"
import { AiFillProduct } from "react-icons/ai"
import { PiBoundingBox, PiStarFill } from "react-icons/pi"
import { MdHeadphones } from "react-icons/md"
import Institute from "./Institute"

const DashVip = () => {
  const [Item, setItem] = useState(0)
  return (
    <div>
      <div style={{backgroundImage:"url('/hero.jpg')"}} className="bg-cover h-[300px] flex justify-center">
        <h1 className="text-center text-[40px] font-bold absolute top-[10%]">A WALLET VIP E INSTITUCIONAL</h1>
      </div>
      <div className="px-[4%] my-[40px]">
        <h1 className="text-[30px] font-bold w-[50%]">Amplifica as tuas vantagens de trading como VIP da Wallet</h1>
        <p className="w-[40%]">Aproveita ao máximo a experiência Wallet desbloqueando benefícios exclusivos com o Programa VIP da Wallet</p>
        <div className="flex gap-4 my-[20px]">
          <button className="bg-[#fcd535] text-[black] font-bold text-[13px] p-2 rounded-[4px]">Contacte-nos</button>
          <button className=" p-2 rounded-[4px] font-bold text-[13px] bg-[#2b3139]">Promoções VIP</button>
        </div>
        <div className="border-[1px] border-[#80808056] flex justify-between p-[2px] rounded-[5px] gap-2">
          {
            vip.map((item, index)=>{
              const pegar = ()=>{
                setItem(item.id)
              }
              return(
                <div onClick={pegar} key={index} className={Item === item.id ?"flex items-center justify-center bg-[#2b31396d] w-full rounded-[5px] cursor-pointer":"flex items-center justify-center  w-full rounded-[5px] cursor-pointer"}>
                  {item.id === 0 ?<BiGift/>:<VscTasklist/>}
                  {item.nome}
                </div>
              )
            })
          }
        </div>
        <div className="grid grid-cols-3 my-[20px] gap-4 ">
          {
            Taxas.map((item, index)=>{

              return(
                <div key={index} className="border-[1px] border-[#80808056] p-2 rounded-[5px] h-[130px] shadow-md">
                 {item.id === 0? <BiDollarCircle className="mb-[20px] text-[25px]"/>:item.id === 1?<AiFillProduct className="mb-[20px] text-[25px]"/>:item.id === 2?<PiBoundingBox className="mb-[20px] text-[25px]"/>:item.id === 3?<MdHeadphones className="mb-[20px] text-[25px]"/>:item.id === 4?<BiDonateHeart className="mb-[20px] text-[25px]"/>:<PiStarFill className="mb-[20px] text-[25px]"/>}
                  <div>
                    <h2 className="font-bold">{item.nome}</h2>
                    <p className="text-[#888da8]">{item.p}</p>
                  </div>
                </div>
              )
            })
          }
        </div>
      </div>
        <div className="flex gap-10 my-[40px]">
          <div className="basis-[70%] pl-[4%]">
            <div className="text-[30px] font-[600] pr-[0px]">Simplifica todas as tuas necessidades de trading com o <span className="text-[#fcd535]">Portal Vip</span></div>
            <button className="my-[20px] p-1 bg-[#484b5c] rounded-[5px] text-[15px] hover:opacity-85">Entrar no Portal VIP</button>
            <div>
              {
                faq.map((item, index)=>{

                  return(
                    <div key={index} className="flex items-center justify-between mb-[20px]">
                      <div className="flex items-center gap-2">
                        <div className={item.id === 1?"border-[1px] border-[#80808056] py-[2px] px-[12px] rounded-[7px]" :"border-[1px] border-[#80808056] py-[4px] px-[10px] rounded-[7px]"}>{item.id}</div>
                        <span className="font-bold">{item.nome}</span>
                      </div>
                      <BiPlus/>
                    </div>
                  )
                })
              }
            </div>
          </div>
          <img src="other.png" alt=""  className="w-[50%] h-[50%] rounded"/>
        </div>
        <Institute/>
    </div>
  )
}

export default DashVip