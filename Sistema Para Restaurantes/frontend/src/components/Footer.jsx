import { FaFacebook, FaInstagram } from "react-icons/fa"
import { footer } from "./object/object"

const Footer = () => {
  return (
    <div className="bg-[black] text-myColor px-[2%] py-5">
      <div className="flex items-center justify-between py-2 max-[580px]:flex-col">
        <div className="flex items-center gap-2 text-[black]">
          <div className="bg-myColor p-1 rounded-[25px] cursor-pointer">
            <FaFacebook/>
          </div>
          <div className="bg-myColor p-1 rounded-[25px] cursor-pointer">
            <FaInstagram/>  
          </div>

        </div>
        <div className="flex items-center font-bold gap-5 uppercase text-[13px] max-[470px]:flex-col max-[470px]:my-4">
          {
            footer.map((item, index)=>{
              return(
                <div key={index} className="cursor-pointer">{item.nome}</div>
              )
            })
          }
          <button className="px-6 py-1 rounded border-myColor border-[2px] text-[13px] hover:scale-[1.2] transition">EMAIL US</button>
        </div>
      </div>
      <div className="text-center text-[13px] font-bold uppercase">
          powered by js
      </div>
    </div>
  )
}

export default Footer