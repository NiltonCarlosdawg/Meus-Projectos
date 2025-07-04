import { useState } from "react"
import { date } from "../components/object/object"
import { BsX } from "react-icons/bs"
import Message from "../components/Message"

const Reserve = ({setShow}) => {
  const [ mydate, setDate ] = useState(0)
  const [selected, setSelected] = useState(0)
  const [showother, setShowOther] = useState(false)
  const [showMessage, setShowMessage] = useState(false)
  const [type, setType] = useState(false)
  const [message, setMessage] = useState("")
  const continuar = ()=>{
    if(selected !== 0){
      setShowOther(!showother)
    }
    else{
      setShowMessage(!showMessage)
      setMessage("Selecione uma data")
      setType(false)
      setTimeout(() => {
        setShowMessage(false)
      }, 3000);
    }
  }
  return (
    <div className="fixed top-0 right-0 h-full w-full backdrop-blur-sm flex items-center justify-center z-10">
      {showMessage&&
        <Message message={message} type={type}/>
      }
      <div className="bg-white w-[35%] h-[500px] rounded shadow-lg p-3 flex items-center flex-col">

        {showother === false?
          <>
            <div className="right-0 text-[30px] flex items-end justify-end w-full"><BsX onClick={()=>setShow(false)} className="cursor-pointer text-red-600"/></div>
          <div className="flex items-center justify-center flex-col">
            <div>
              <h2 className="font-[quick] text-[25px] my-4 text-center">Em que dia gostaria de fazer a reserva?</h2>
            </div>
            <table className="">
              <thead>
                <tr className="bg-yellow-100">
                  <th className="py-2 px-3">DOM</th>
                  <th className="py-2 px-3">SEG</th>
                  <th className="py-2 px-3">TER</th>
                  <th className="py-2 px-3">QUA</th>
                  <th className="py-2 px-3">QUI</th>
                  <th className="py-2 px-3">SEX</th>
                  <th className="py-2 px-3">SÁB</th>
                </tr>
              </thead>
              <tbody className="text-center">
                <tr>
                  {
                    date.map((item, index)=>{
                      const pegar=()=>{
                        setDate(item)
                        setSelected(item)
                      }
                      return(
                        <td onClick={pegar} className={item === mydate?"p-2 cursor-pointer border bg-yellow-200 text-yellow-950" :"p-2 cursor-pointer border"} key={index}>{item}</td>
                      )
                    }).splice(0, 7)
                  }
                </tr>
                <tr>
                  {
                    date.map((item, index)=>{
                      const pegar=()=>{
                        setDate(item)
                        setSelected(item)
                      }
                      return(
                        <td onClick={pegar} className={item === mydate?"p-2 cursor-pointer border bg-yellow-200 text-yellow-950" :"p-2 cursor-pointer border"} key={index}>{item}</td>
                      )
                    }).splice(7, 7)
                  }
                </tr>
                <tr>
                  {
                    date.map((item, index)=>{
                      const pegar=()=>{
                        setDate(item)
                        setSelected(item)
                      }
                      return(
                        <td onClick={pegar} className={item === mydate?"p-2 cursor-pointer border bg-yellow-200 text-yellow-950" :"p-2 cursor-pointer border"} key={index}>{item}</td>
                      )
                    }).splice(14, 7)
                  }
                </tr>
                <tr>
                  {
                    date.map((item, index)=>{
                      const pegar=()=>{
                        setDate(item)
                        setSelected(item)
                      }
                      return(
                        <td onClick={pegar} className={item === mydate?"p-2 cursor-pointer border bg-yellow-200 text-yellow-950" :"p-2 cursor-pointer border"} key={index}>{item}</td>
                      )
                    }).splice(21, 7)
                  }
                </tr>
                <tr>
                  {
                    date.map((item, index)=>{
                      const pegar=()=>{
                        setDate(item)
                        setSelected(item)
                      }
                      return(
                        <td onClick={pegar} className={item === mydate?"p-2 cursor-pointer border bg-yellow-200 text-yellow-950" :"p-2 cursor-pointer border"} key={index}>{item}</td>
                      )
                    }).splice(28, 3)
                  }
                </tr>
              </tbody>
            </table>
            <button onClick={continuar} className="bg-yellow-200 font-bold text-[13px] w-full py-2 rounded my-4 active:scale-[1.2]">Continuar</button>

          </div>
          </>:
          <div className="w-[90%]">
            <div className="right-0 text-[30px] flex items-end justify-end w-full"><BsX onClick={()=>setShow(false)} className="cursor-pointer text-red-600"/></div>
            <h2 className="text-center font-[quick] text-[25px] my-8 font-bold">Insira o seus Dados</h2>
            <div action="" className="flex items-center flex-col gap-4 justify-center">
              <input type="text" id="nome" placeholder="Digite o seu nome" className="py-2 px-3 bg-gray-100 border border-[#80808056] text-[13px] rounded w-full"/>
              <input type="email" id="email" placeholder="Digite o seu Email" className="py-2 px-3 bg-gray-100 border border-[#80808056] text-[13px] rounded w-full"/>
              <div className="flex items-center w-full gap-4 my-4">
                <button onClick={()=>setShowOther(false)} className="px-4 py-2 bg-yellow-300 rounded font-bold text-[13px] w-full">Voltar</button>
                <button className="px-4 py-2 bg-yellow-300 rounded font-bold text-[13px] w-full">Enviar</button>
              </div>
            </div>
          </div>
          }
        
      </div>
    </div>
  )
}

export default Reserve