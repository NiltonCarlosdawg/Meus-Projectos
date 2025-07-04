import { BiDollar, BiHome } from "react-icons/bi"
import { side } from "./Object"
import { RiTeamFill } from "react-icons/ri"
import { GrIntegration } from "react-icons/gr"
import { PiInvoice } from "react-icons/pi"
import { useState } from "react"

const Command = () => {
  const [myId, setMyId] = useState(0)
  return (
    <div className="space-y-1 ">
      <ul>
        {
          side.map((item, index)=>{
            const pegar =()=>{
              setMyId(item.id)
            }
            return(
              <li key={index} onClick={pegar} className={item.id === myId?"cursor-pointer py-1 text-black bg-[#fff]  flex items-center gap-1 rounded px-2 shadow-md font-[400] mb-2":"cursor-pointer py-1 flex items-center gap-1 px-2 text-[gray] font-[400] mb-2"}>
                {item.id === 0 ?<><BiHome/>{item.nome}</> :item.id === 1?<><RiTeamFill/>{item.nome}</>:item.id === 3?<><GrIntegration/>{item.nome}</>:item.id === 4?<><BiDollar/>{item.nome}</>:item.id === 2?<><PiInvoice/>{item.nome}</>:<></>}
              </li>
            )
          })
        }
      </ul>
    </div>
  )
}

export default Command