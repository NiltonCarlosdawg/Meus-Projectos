import { useNavigate } from "react-router-dom"
import Reserve from "../../pages/Reserve"
import { nav } from "../object/object"
import { useState } from "react"
import NewReserve from "../../pages/NovaReserva"
import { BiLogOut } from "react-icons/bi"
import { GrUserAdmin } from "react-icons/gr"

const Navbar = () => {
    const [show, setShow] = useState(false)
    const navigate = useNavigate()
    const [date, setDate] = useState([])
    let username = localStorage.getItem("user")
    const className = "max-[980px]:hidden"
    const [man, setMan] = useState(false)
    const mine = ()=>{
      for(let n = 0; n <=31;n++){
        setDate(prev=>[...prev, {num:n}])
      }
    }
    const sendto=(item)=>{
      if(item === "Reserve"){
        setShow(!show)
        mine()
      }
      else if(item === "Menus"){
        navigate("/menu")
      }
      else if(item === "Sobre"){
        navigate("/about")
      }
      else if(item === "Home"){
        navigate("/")
      }
      else if(item === "Contactos"){
        navigate("/contact")
      }
  
    }
    const logout = ()=>{
      localStorage.removeItem("user")
      window.location.reload()
      console.log(username)
    }
  return (

    <>
      {show&&
        <div>
          <NewReserve date={date} setShow={setShow}/>
        </div>
      }
      <div className="px-[2%] py-3 bg-[black] text-myColor">

      <nav className="flex items-center justify-between">
      <div className="font-bold text-[25px] cursor-pointer" onClick={()=>navigate("/")}>MON SOIR</div>
        <div className="cursor-pointer hidden max-[980px]:block" onClick={()=>setMan(!man)}>
          <div className="w-[32px] h-[2px] bg-myColor m-2"></div>
          <div className="w-[32px] h-[2px] bg-myColor m-2"></div>
          <div className="w-[32px] h-[2px] bg-myColor m-2"></div>
        </div>
        <div className={`max-[980px]:absolute z-[2] max-[980px]:top-[11vh] max-[980px]:right-0 max-[980px]:w-[50vw] max-[980px]:h-[92vh] max-[980px]:bg-[black] flex items-center justify-between basis-[64%] max-[980px]:flex-col max-[980px]:justify-around ${man?className:""} faq`} >
          <ul className="flex items-center gap-2 font-semibold uppercase text-[14px] max-[980px]:flex-col">
            {
              nav.map((item, index)=>{
                return(
                  <li key={index} onClick={()=>sendto(item.nome)} className="cursor-pointer hover:text-[black] rounded py-1 px-2 transition-[0.5s] hover:bg-myColor">{item.nome}</li>
                )
              })
            }
          </ul>
          {username === null?<div className="divide-x divide-yellow-950 flex items-center max-[980px]">
            <button className="px-3 py-2 font-bold text-[13px] transition hover:bg-myColor hover:text-[black] rounded-l" onClick={()=>navigate("/login")}>Log in</button>
            <button className="px-3 py-2 font-bold text-[13px] bg-[] hover:bg-myColor hover:text-[black] rounded-r" onClick={()=>navigate("/cadastro")}>Sign in</button>
          </div>:username != null && username != "eddiendulo@gmail.com"?<><div className="cursor-pointer" onClick={logout}><BiLogOut size={30} /></div></>:username == "eddiendulo@gmail.com"?<div className="flex items-center divide-x divide-yellow-950"><button className="px-4" onClick={()=>navigate("/admin")}><GrUserAdmin size={30}/></button><div className="cursor-pointer px-4" onClick={logout}><BiLogOut size={30} /></div></div>:""}
        </div>
      </nav>
    </div>
    
    </>

  )
}

export default Navbar