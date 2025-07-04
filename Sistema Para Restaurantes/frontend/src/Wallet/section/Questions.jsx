import { BiPlus } from "react-icons/bi"
import { Perguntas, respostas } from "../object/myobject"
import { useState } from "react"
import { BsSignDoNotEnter } from "react-icons/bs"

const Questions = () => {
  const [info, setInfo] = useState(false)
  const [cartshit, setCartShit] = useState("")
  const [idselect, setIdSelect] = useState(10)
  const Takeid =(id)=>{
    setIdSelect(id)
    if(idselect === id){
      setIdSelect(10)
    }
    setInfo(!info)
    respostas.map((item, index)=>{
      if(item.id === id){
        setCartShit(item.nome)
      }
    })
  }
  return (
    <div className="py-[40px] px-[2%]">
      <h1 className="text-[31px] text-center font-bold font-[var]">Perguntas Frequentes</h1>
      <div className="text-[15px]">
        {
          Perguntas.map((item, index)=>{
            return(
              <div key={index} className={idselect === item.id? " bg-[#7d84942d] rounded-[10px] px-1 py-[10px] font-[500]":"mb-[20px]"}>
                <div className={"flex items-center justify-between px-2 py-2 rounded-[5px]  text-[17px] font-[quick] cursor-pointer"} onClick={()=>Takeid(item.id)}>
                  <div>
                    <span className="border-[1px] rounded-[5px] px-[10px] mr-[10px] border-[#80808042]">{item.id + 1}</span>
                    <span>{item.nome}</span>
                  </div>
                  {item.id === idselect ?<BsSignDoNotEnter/> :<BiPlus/>}
                </div>
                {idselect === item.id&&<div className="px-[2%] text-[#888da8]">
                  <div>
                    {cartshit}
                  </div>
                
                </div>}
              </div>
       
            )
          })
        }
        
      </div>
    </div>
  )
}

export default Questions