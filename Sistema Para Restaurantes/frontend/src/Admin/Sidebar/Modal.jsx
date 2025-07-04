import { BiLink, BiPlus, BiX } from "react-icons/bi"
import { BsEye } from "react-icons/bs"
import { LuContact } from "react-icons/lu"
import { forsearch } from "./Object"
import { FaSignOutAlt } from "react-icons/fa"
import { useEffect, useState } from "react"

const Modal = ({setOpenCommand}) => {
  const [search, setSearch] = useState("")
  const [searchData, setSearchData] = useState([])

  useEffect(()=>{
    let filter = forsearch;

    if(search !== ''){
        filter = filter.filter(item=>(item.nome.toLowerCase().includes(search.toLowerCase())))
    }
    setSearchData(filter)
  }, [search])
  return (
    <div className="fixed w-full h-full top-0 right-0 flex justify-center bg-[#00000038] backdrop-blur">
      <div className="bg-[white] w-[30%] h-[280px] rounded-sm absolute top-[20%] shadow-xl">
        <div className="border-b-[#80808056] border-b-[1px] p-2 flex items-center">
          <input type="text" placeholder="O que que precisas?" className="bg-[#fff0] w-full outline-none" onChange={(e)=>setSearch(e.target.value)}/>
          <BiX size={30} className="cursor-pointer animate-bounce" onClick={()=>setOpenCommand(false)}/>
        </div>

        <div className="px-2">
          {
            searchData.map((item,index)=>{
              return(
                <div key={index}>
                  <h1 className="text-[gray] text-[14px]">{item.other}</h1>
                  <div className="flex items-center gap-1  mb-1 cursor-pointer hover:bg-[#8080805d] p-1 rounded-md">
                   <span>{item.id === 0?<><BiPlus/></>:item.id === 1?<><BsEye/></>:item.id === 2?<><BiLink/></>:item.id === 3?<><LuContact/></>:<></>}</span>
                   <span>{item.nome}</span>
                  </div>
        
                </div>
              )
            })
          }
          <button className="p-1 bg-[black] text-[white] flex items-center w-full rounded-md"><FaSignOutAlt/>Sign Out</button>
        </div>
      </div>
    </div>
  )
}

export default Modal