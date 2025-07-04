import { useState } from "react"
import { marks } from "./Object"
import Overview from "./Overview"
import Datatrading from "./Datatrading"

const DashMark = () => {
  const [myid, setMyId] = useState(0)
  return (
    <div className="px-[4%] my-[20px]">
      <div className="flex items-center gap-5 text-[25px] font-[600] font-[var]">
        {
          marks.map((item, index)=>{
            const pegar = ()=>{
              setMyId(item.id)
            }
            return(
              <div key={index} onClick={pegar} className={item.id === myid?"cursor-pointer":"cursor-pointer text-[#676a7e]"}>
                {item.nome}
              </div>
            )
          })
        }
      </div>
      {
        myid === 0?<Overview/>:myid === 1?<Datatrading/>:<></>
      }
    </div>
  )
}

export default DashMark