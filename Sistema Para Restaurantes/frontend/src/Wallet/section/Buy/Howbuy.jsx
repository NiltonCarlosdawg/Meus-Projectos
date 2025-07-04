import { RiSecurePaymentFill } from "react-icons/ri"
import { how } from "./object"

const Howbuy = () => {
  return (
    <div className="px-[4%]">
      <h1 className="text-[35px] font-[500] font-[var] mb-[20px]">Como comprar crypto</h1>
      <div className="grid grid-cols-3 gap-4">
        {
          how.map((item, index)=>{
            return(
              <div key={index} className="border-[1px] border-[#80808056] p-2 rounded-[4px]">
                <img src="usd.png" className="w-[20%] mb-[20px] h-[50]" alt="" />
                <div>
                  <h1 className="font-[600] text-[19px] font-[var]">{item.nome}</h1>
                  <p className="text-[#888da8] text-[15px]">{item.info}</p>
                </div>
              </div>
            )
          
          })
        }
      </div>
    </div>
  )
}

export default Howbuy