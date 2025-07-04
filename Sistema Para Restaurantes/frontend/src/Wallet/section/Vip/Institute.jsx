import { useState } from "react"
import { morecri, moregrid } from "./object"

const Institute = () => {
  const [id, setId] = useState(0)
  return (
    <div className="bg-[#0b0e11] px-[4%] py-10">
      <div>
        <h1 className="text-[25px] font-bold pr-[70%]">Serviços institucionais de um líder global em trading de criptomoeda</h1>
        <p className="pr-[50%] my-[20px]">Transaciona, constrói, protege e desenvolve os teus ativos com o nosso pacote completo de produtos e serviços de criptomoeda profissionais</p>
        <button className="bg-[#fcd535] p-1 text-[13px] font-bold rounded-[5px] text-[black]"><div className="animate-bounce-twice">Contacte-nos</div></button>
      
      </div>
      <div>
          <div className="border-[#80808056] border-[1px] rounded-md flex justify-between gap-2 p-0.5 text-center font-[600] my-[20px]">
            {
              morecri.map((item, index)=>{
                const pegar =()=>{
                  setId(item.id)
                }
                return(
                  <div key={index} onClick={pegar} className={item.id === id?"w-full bg-[#2b31396d] cursor-pointer":"w-full cursor-pointer text-[#888da8]"}>
                    {item.nome}
                  </div>
                )
              })
            }
          </div>
          <div>
            <div className="grid grid-cols-2 gap-4">
              {
                moregrid.map((item, index)=>{
                  return(
                    <div key={index} className="border-[1px] border-[#80808056] p-2 rounded-sm">
                      <h1 className="font-[600] text-[20px]">{item.h1}</h1>
                      <p className="text-[#888da8] my-2 text-[14px]">{item.p}</p>
                      <button className="border-[1px] border-[#80808056] py-1 px-3 rounded-md">
                        {">"}
                      </button>
                    </div>
                  )
                })
              }
            </div>
            <div className="text-center my-[20px]">
              <h1 className="text-[35px] font-bold">DESCOBRE A BINANCE VIP E INSTITUCIONAL</h1>
              <p className="text-[#888da8] my-[10px]">Contacta um gestor de conta para saberes mais sobre as soluções de criptomoeda para clientes privados e instituções</p>
              <button className="rounded-sm p-2 text-[black] font-bold text-[13px] bg-[#fcd535]">Contacte-nos</button>
            </div>
          </div>
      </div>
    </div>
  )
}

export default Institute