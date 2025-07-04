import { useState } from "react"
import { mainmenu } from "../object/object"
import Menu from "./Menu"

const Mainmenu = () => {
  const [type, setType] = useState("Menu")
  return (
    <div>
      <div className="flex items-center justify-center gap-4 uppercase my-8 max-[450px]:flex-col">
        {
          mainmenu.map((item, index)=>{
            const pegar=()=>{
              setType(item.nome)
            }
            return(
              <div key={index} onClick={pegar} className={item.nome === type?"font-bold text-[13px] py-1 px-5 text-myColor bg-[black] border-[2px] border-[black] cursor-pointer hover:scale-[1.2] transition":" border-[2px] border-[black] font-bold text-[13px] py-1 px-5 cursor-pointer hover:scale-[1.2] transition"}>
                {item.nome}
              </div>
            )
          })
        }
      </div>
      <div>
        <p className="px-[25%] text-[13px] my-4 text-center">Veja o nosso menu aqui e nos siga nas nossas redes sociais para ouvir sobre os nossos especiais semanais e algumas ofertas pra todos os paladares</p>
      </div>
      <div>
        <Menu type={type}/>
      </div>
    </div>
  )
}

export default Mainmenu