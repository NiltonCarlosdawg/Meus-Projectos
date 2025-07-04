import { BiCircleQuarter, BiDownload, BiLogoBitcoin, BiSearch } from "react-icons/bi"
import { advanced, basic, Navelements } from "../object/myobject"
import { RiGlobalFill } from "react-icons/ri"
import { BsMoonFill, BsSunFill } from "react-icons/bs"
import { useEffect, useState } from "react"
import { useDispatch, useSelector } from "react-redux"
import { changeboolean, selectboolean, setboolean } from "../Redux/Bslice"
import { CgAnchor } from "react-icons/cg"
import { FaPeopleGroup } from "react-icons/fa6"
import { TbCircleArrowDownFilled } from "react-icons/tb"
import { PiBoot } from "react-icons/pi"
import { FaCopy } from "react-icons/fa"
import { ImConnection } from "react-icons/im"
import { useNavigate } from "react-router-dom"

const Nav = ({darkmode}) => {
  const dispatch = useDispatch()
  const [showinput, setShowInput] = useState(false)
  const myvalue = useSelector(state=>state.boolean.boolean)
  const [marks, setmarks] = useState(false)
  const navigate = useNavigate()
  const ativate =()=>{
    dispatch(setboolean(darkmode))
  }
  const desativate =()=>{
    dispatch(changeboolean(myvalue))
  }
  const onMouse =()=>{
      setmarks(true)
  }
  const Out = ()=>{
    // if(marks === true){
    //   setmarks(false)
    // }
    // console.log("55")

  }
  useEffect(()=>{
    const theme = {
      dark:true,
      light:false
    }
    const selectedTheme = localStorage.setItem("theme",`${myvalue}`)
    if(selectedTheme){
      document.body.classList.add(selectedTheme)
    }
    else if(window.matchMedia("(prefers-color-scheme: dark)").matches){
      document.body.classList.add("dark")
    }
    else{
      document.body.classList.add("light")
    }
  },[myvalue])
  return (
    <nav className="flex items-center justify-between px-[2%] pb-[10px]">
      <div className="flex items-center gap-[20px]">
        <div className="text-[25px] text-[#f0b90b] font-bold"><a href="http://localhost:5173/">Wallet</a> </div>
        <ul className="flex gap-[15px]">
          {
            Navelements.map((item, index)=>{
              const sendbro=()=>{
                if(item.nome === "Buy Crypto"){
                  navigate("/buy")
                }
                else if(item.nome === "Markets"){
                  navigate("/markets")
                }
                else if(item.nome === "Vip"){
                  navigate("/vip")
                }
              }
              const onMouse =()=>{
                if(item.nome === "Trade"){
                  setmarks(true)
                }
              }
              const Out =()=>{
                setmarks(false)
              }

              return(
                <li onClick={sendbro} className="cursor-pointer hover:opacity-[60%]" onMouseEnter={onMouse} onMouseOut={Out} key={index}>{item.nome === "Trade"?<a href="http://localhost:5173/trade">Trade</a> : item.nome}</li>
              )
            })
          }
          {marks &&<div onMouseEnter={onMouse} className={myvalue?"absolute flex bg-[#dddce3] px-[10px] py-2 text-[14px] rounded-[5px] top-[7%] w-[40%] font-[quick] gap-[10px]" :"absolute flex bg-[#1e2329] px-[10px] py-2 text-[14px] rounded-[5px] top-[7%] w-[40%] font-[quick] gap-[10px]"}>
              <div>
                <h1 className="mb-[20px] font-bold">Basic</h1>
                {
                  basic.map((item, index)=>{
                    const sendtotrade=()=>{
                      if(item.nome === "Spot"){
                        navigate("/trade")
                      }
                    }
                    return(
                      <div key={index} onClick={sendtotrade} className={myvalue ? "hover:bg-[#ebeece] cursor-pointer":"hover:bg-[#4d5a6a] cursor-pointer"}>
                        <p className="text-[17px] font-bold flex items-center gap-2 ">{item.nome === "Spot"?<BiLogoBitcoin color="#957f27"/>:item.nome === "Margin"?<CgAnchor color="#957f27"/>:item.nome === "P2P"?<FaPeopleGroup color="#957f27"/>:<TbCircleArrowDownFilled color="#957f27"/>}{item.nome}</p>
                        <span className={myvalue?"text-black font-[500]" :"text-[#888da8] font-[600]"}>{item.info}</span>
                      </div>
                    )
                  })
                }
              </div>
              <div >
                <h1 className="mb-[20px] font-bold">Advanced</h1>
                {
                  advanced.map((item, index)=>{

                    return(
                      <div className={myvalue ? "hover:bg-[#ebeece] cursor-pointer":"hover:bg-[#4d5a6a] cursor-pointer"} key={index}>
                        <p className="text-[17px] font-bold flex items-center gap-2 ">{item.nome === "Trading Bots"?<PiBoot color="#957f27"/>:item.nome === "Copy Trading"?<FaCopy color="#957f27"/>:<ImConnection color="#957f27"/>}{item.nome}</p>
                        <span className={myvalue?"text-black font-[500]" :"text-[#888da8] font-[600]"}>{item.info}</span>
                      </div>
                    )
                  })
                }
              </div>
              <div >

              </div>
          </div>}
        </ul>
      </div>
      <div className="flex items-center gap-[20px]">
        <div className={showinput ?"flex bg-[#c0c0c03e] rounded-[5px] border-[1px] border-[#8080808b] items-center p-1" :"flex rounded-[5px] items-center p-1"}>
          {showinput &&<input className="text-black bg-[#c0c0c000] w-[140px] px-[3px]"/>}
          <BiSearch size={20} className="cursor-pointer" onClick={()=>setShowInput(!showinput)}/>
        </div>
        <button className={myvalue ?"bg-[#eaecef] p-1 rounded-[5px]" :"bg-[#2b3139] p-1 rounded-[5px]"}>Log in</button>
        <button className="bg-[#fcd535] text-[black] p-1 rounded-[5px]">Sign Up</button>
        <button><BiDownload/></button>
        <button><RiGlobalFill/></button>
        <button>{myvalue ?<BsSunFill onClick={desativate}/> :<BsMoonFill onClick={ativate}/>}</button>
      </div>
    </nav>
  )
}

export default Nav