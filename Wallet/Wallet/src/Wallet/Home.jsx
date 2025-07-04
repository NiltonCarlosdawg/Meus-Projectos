import { useState } from "react"
import Nav from "./header/Nav"
import { useDispatch, useSelector } from "react-redux"
import Dashbody from "./section/home/Dashbody"
import Questions from "./section/Questions"
import Today from "./section/Today"
import Footer from "./footer/Footer"
import DashHero from "./section/home/DashHero"

const Home = () => {
  const [darkmode, setDarkmode] = useState(false)
  const myvalue = useSelector(state=>state.boolean.boolean)
  return (
    <div className={myvalue ?"bg-gradient text-[black] py-[10px] transition-[1s]":"bg-[#181a20] text-[#fff]  py-[10px] transition-[1s]"}>
      <Nav darkmode={darkmode}/>
      <DashHero/>
      <Dashbody/>
      <Questions/>
      <Today/>
      <Footer/>
    </div>
  )
}

export default Home