import { CiBitcoin } from "react-icons/ci"
import { popular } from "../../object/myobject"
import { FaEthereum } from "react-icons/fa6"
import { RiBnbFill } from "react-icons/ri"
import { SiRipple, SiSolana } from "react-icons/si"
import { useState } from "react"
import { useSelector } from "react-redux"

const DashHero = () => {
  const [myId, setMyId] = useState(0)
  const myvalue = useSelector(state=>state.boolean.boolean)
  const thead = [
    {
      id:0,
      nome:"Popular"
    },
    {
      id:1,
      nome:"New Listing"
    }
  ]
  const currency =(amount)=>{
 
    if(String(amount).includes(",")){
      amount = Number(amount.replace(",", "."))
    }
    const pamount = amount 
    const decimals = 2
    const valorFormatado = pamount.toLocaleString('USA', {
      style:"currency",
      currency:"usd",
      min:decimals,
      max:decimals
    })

    return valorFormatado
  }

  
  return (
    <div className="flex items-center justify-between px-[6%] mt-[60px] mb-[100px]">
      <div>
        <div className="text-[90px] font-[600]">
          <div className="">
            <h1 className="text-[#f0b90b]">214,739,629 </h1>
            <p>TRUST US</p>
            <span>USERS</span>
          </div>
        </div>
        <div className="flex gap-5">
          <input placeholder="Email/Phone Number" className="bg-[#80808000] border-[1px] border-[#888da8] p-1 rounded-[5px]"/>
          <button className="bg-[#fcd535] p-1 rounded-[5px] text-[black] px-[20px]">Sign up</button>
        </div>
      </div>
      <div className="w-[35%]">
        <div className={myvalue? "bg-[#80808058] rounded-[5px] p-4":"bg-[#1e2329] rounded-[5px] p-4"}>
          <div>
            <div className="flex justify-between">
              <div className="flex gap-[10px]">       
                {
                  thead.map((item, index)=>{
                    const pegar=()=>{
                      setMyId(item.id)
                    }
                    return(
                      <div onClick={pegar} key={index} className={item.id === myId ?"cursor-pointer border-b-[4px] border-[#7d6a1f]" :"cursor-pointer"}>{item.nome}</div>
                    )
                  })
                }
                </div>
                <button className="text-[14px] text-[#888da8]">View All coins</button>
            </div>
            <table>
              <thead>
                <tr>
                  <th className=""></th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {myId === 0?
                  popular.map((item, index)=>{
                    return(
                      <tr key={index}>
                        <td className="pr-[60px] py-1 flex items-center">{item.nome === "BTC"?<CiBitcoin className="text-[20px] text-[#957f27] mr-[5px]"/> :item.nome === "ETH"?<FaEthereum className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "BNB"?<RiBnbFill className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "XRP"?<SiRipple className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "SOL"? <SiSolana className="text-[20px] text-[#957f27] mr-[5px]"/>:<></>}{item.nome} <span className="text-[13px] text-[#888da8]">{item.sub}</span></td>
                        <td className="pr-[80px]">{currency(item.price)}</td>
                        <td className="flex items-center justify-center">{item.change < 0 ?<span className="text-[red]">{item.change}%</span> :<span className="text-[green]">{item.change}%</span>}</td>
                      </tr>
                    )
                  }):
                  popular.map((item, index)=>{
                    return(
                      <tr key={index}>
                        <td className="pr-[60px] py-1 flex items-center">{item.nome === "BTC"?<CiBitcoin className="text-[20px] text-[#957f27] mr-[5px]"/> :item.nome === "ETH"?<FaEthereum className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "BNB"?<RiBnbFill className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "XRP"?<SiRipple className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "SOL"? <SiSolana className="text-[20px] text-[#957f27] mr-[5px]"/>:<></>}{item.nome} <span className="text-[13px] text-[#888da8]">{item.sub}</span></td>
                        <td className="pr-[80px]">{currency(item.price)}</td>
                        <td className="flex items-center justify-center">{item.change < 0 ?<span className="text-[red]">{item.change}%</span> :<span className="text-[green]">{item.change}%</span>}</td>
                      </tr>
                    )
                  }).reverse()
                }
              </tbody>
            </table>
          </div>
        </div>
        <div>
            <div className={myvalue? "bg-[#80808058] rounded-[5px] p-4 mt-[20px]":"bg-[#1e2329] rounded-[5px] p-4 mt-[20px]"}>
              <div className="flex justify-between items-center mb-[5px]">
                <span>News</span>
                <span className="text-[14px] text-[#888da8] cursor-pointer">View All News</span>
              </div>

              <div className={myvalue ?"font-[400] text-[#303032]" :"font-[400] text-[#a7a9af]"}>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Blanditiis dignissimos et obcaecati eius necessitatibus saepe dolor, eos nobis, culpa corporis dolores, quidem rem perferendis repudiandae ab dolorem laboriosam atque harum.
              </div>
            </div>    
        </div>
      </div>
     
    </div>
  )
}

export default DashHero