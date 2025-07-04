import { useEffect, useState } from "react"
import { barbecue, sand } from "../object/food"
import { GrAddCircle } from "react-icons/gr"
import NovoProduto from "./NovoProduto"

const Menu = ({type}) => {
  const [ databarb, setDataBarb] = useState([])
  const [newProd, setNewProd] = useState(false)
  const username = localStorage.getItem("user")
  useEffect(()=>{
    let filter = barbecue
    if(type === "Menu"){
      setDataBarb(barbecue)
    }
    else{
      if(filter !== ""){
        filter = filter.filter(item=>(item.type.includes(type)))
      }
      setDataBarb(filter)
    }

  }, [type])
  console.log(newProd)
  return (
    <div className="flex gap-12 px-[8%] py-8 max-[880px]:flex-col">
      {username === "eddiendulo@gmail.com"&&<div className="fixed z-[2] top-[400px] right-8 bg-white rounded-[50%] flex items-center justify-center p-2 shadow-lg">
        <button className="text-[40px]"><GrAddCircle onClick={()=>setNewProd(!newProd)}/></button>
      </div>}
      {newProd&&
        <NovoProduto setNewProd={setNewProd}/>
      }
      <div>
        <div>
          <h2 className="text-[50px] max-[700px]:text-[40px] font-bold font-[drip] uppercase max">Churrasco</h2>
          <p className="font-[600] font-[quick] text-[13px] mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ea recusandae tenetur autem reprehenderit officiis</p>
          <img src="food (7).jpg" alt="" className="" />
        </div>
        <div className="mt-6">
          <div>
            {
              barbecue[0].subinfo.map((item, index)=>{

                return(
                  <div key={index} className="text-[16px] uppercase font-[popbold] my-2">
                    {item}
                  </div>
                )
              })
            }
          </div>
     
          <img src="food (15).jpg" alt="" />
          <div className="border-b-[2px] border-[black] ">
            {
              databarb.map((item, index)=>{
                return(
                  <div key={index} className="text-[16px] uppercase font-[popbold] my-2">
                    {item.nome}
                  </div>
                )
              }).splice(1, 2)
            }
          </div>
          <div>
            <h2 className="text-[50px] font-bold font-[drip] uppercase max-[700px]:text-[40px]">PRATOS E COMBOS</h2>
            <p className="text-[13px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Praesentium, eaque, dicta similique dolores!</p>
          </div>
          <div>
            {
              barbecue.map((item,index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(3, 3)
            }
            <img src="food (20).jpg" alt="" />
          </div>
          <div className="border-b-[2px] border-[black]">
            {
              barbecue.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(6, 2)
            }
          </div>
          <h2 className="text-[50px] font-bold font-[drip] uppercase max-[700px]:text-[40px]">Partes</h2>
          <p className="text-[13px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus officiis consequuntur dicta, quae suscipit iure </p>
          <div>
          {
              barbecue.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(8, 3)
            }
          </div>
        </div>
      </div>



      <div>
        <div>
          <h2 className="text-[50px] font-bold font-[drip] uppercase max-[700px]:text-[40px]">Sandwiches</h2>
          <p className="font-[600] font-[quick] text-[13px] mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora eos, ex unde dolorem autem ducimus porro</p>
          <img src="food (17).jpg" alt="" className="" />
        </div>
        <div className="mt-6">
          <div>
            {
              sand.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h2 className="text-[16px] uppercase font-[popbold]">{item.nome}</h2>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(0, 3)
            }
          </div>
          <div>
            <img src="food (14).jpg" alt="" />
          </div>

          <div>
            {
              sand.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(3, 3)
            }
            <img src="food (5).jpg" alt="" />
          </div>
          <div className="border-b-[2px] border-[black]">
          {
              sand.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(6, 2)
            }
            <img src="food (3).jpg" alt="" />
            {
              sand.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(8, 1)
            }
          </div>
          <h2 className="text-[40px] font-bold font-[drip] uppercase max-[700px]:text-[30px]">Sandwiches Specials</h2>
          <p className="text-[13px]">Pega enquanto ainda tem!</p>
          <div>
          {
              sand.map((item, index)=>{
                return(
                  <div key={index} className="my-2">
                    <h3 className="text-[16px] uppercase font-[popbold]">{item.nome}</h3>
                    <p className="text-[13px]">{item.info}</p>
                  </div>
                )
              }).splice(3, 1)
            }
          </div>
        </div>
      </div>
    </div>
  )
}

export default Menu