import { CiBitcoin } from "react-icons/ci"
import { popular } from "../object/myobject"
import { FaEthereum } from "react-icons/fa6"
import { RiBnbFill } from "react-icons/ri"
import { SiRipple, SiSolana } from "react-icons/si"
import { useDispatch } from "react-redux"
import { setCoin } from "../Redux/CoinSlice"

const LIstCry = ({setBit}) => {
  const dispatch = useDispatch()
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
    <div className="border-[1px] border-[#80808056] rounded-[4px] ">
      <h1 className="p-[4px] bg-[#00000034] rounded-t-[4px]">Hot cryptos</h1>
      <table className="">
        <thead className="border-b-[1px] border-b-[#80808056]">
          <tr>
            <th className=""></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          {
            popular.map((item, index)=>{
              const pegar =()=>{
                setBit(item.nome)
                dispatch(setCoin(item.nome))
              }
              return(
                <tr key={index} className="">
                  <td onClick={pegar} style={{cursor:"pointer"}} className="pl-[10px] pr-[60px] py-1 flex items-center">{item.nome === "BTC"?<CiBitcoin className="text-[20px] text-[#957f27] mr-[5px]"/> :item.nome === "ETH"?<FaEthereum className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "BNB"?<RiBnbFill className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "XRP"?<SiRipple className="text-[20px] text-[#957f27] mr-[5px]"/>:item.nome === "SOL"? <SiSolana className="text-[20px] text-[#957f27] mr-[5px]"/>:<></>}{item.nome} <span className="text-[13px] text-[#888da8]">{item.sub}</span></td>
                  <td className="pr-[80px]">{currency(item.price)}</td>
                  <td className="flex items-center justify-center pr-[10px]">{item.change < 0 ?<span className="text-[red]">{item.change}%</span> :<span className="text-[green]">{item.change}%</span>}</td>
                </tr>
              )
            })
          }
        </tbody>
      </table>
    </div>
  )
}

export default LIstCry