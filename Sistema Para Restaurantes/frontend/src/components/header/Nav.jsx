import { FaArrowRight } from "react-icons/fa"
import { nav } from "../object/object"
import "./nav.css"
const Nav = () => {

  return (
    <div className="px-[2%] py-4">
      <nav className="bg-slate-200 shadow-xl shadow-[#00000016] pl-[2%] flex justify-between position z-10" id="position">
        <div className="flex flex-col text-[35px] font-[popbold] font-extrabold space-y-[-20px]"><span className="text-[#95c11e]">FESTI</span><span className="text-[#5691cc]">NET</span></div>
        <ul className="flex items-center gap-4 font-bold">
          {
            nav.map((item, index)=>{
              return(
                <li key={index} className="cursor-pointer">
                  {item.nome}
                </li>
              )
            })
          }
        </ul>
        <button className="flex items-center bg-[#f9cc04] px-4 gap-1 text-[13px] font-bold"><span>Entrar</span> <FaArrowRight/></button>
      </nav>
    </div>
  )
}

export default Nav