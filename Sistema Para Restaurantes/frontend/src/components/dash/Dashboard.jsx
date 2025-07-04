import { useNavigate } from "react-router-dom"
import { cate, gridtwo, oneItem } from "../object/object"

const Dashboard = () => {
  const navigate = useNavigate()
  return (
    <div className=" bg-myColor pt-8">
      <div className="grid grid-cols-2 gap-4 max-[980px]:grid-cols-1">
        {
          gridtwo.map((item, index)=>{
            return(
              <div key={index} className="flex items-center flex-col">
                <div className="px-12">
                  <img src={item.link} alt="" className="w-[100%] rounded transition my-4"/>
                </div>

                <h2 className="text-[30px] font-bold uppercase">{item.nome}</h2>
                <p className="text-center px-[20%] text-sm">{item.info}</p>
              </div>
            )
          })
        }
      </div>
      <div className="mt-32">
        {
          oneItem.map((item, index)=>{
            return(
              <div key={index} className="flex max-[980px]:flex-col">
                <div className="basis-[90%] text-center">
                  <h2 className="text-[40px] font-bold">{item.nome}</h2>
                  <p className="px-[20%] text-[13px]">{item.info}</p>
                  <button className="border-[2px] rounded p-1 border-[black] font-bold text-[13px] my-4 hover:scale-[1.3] transition" onClick={()=>navigate("/menu")}>Veja o nosso Menu</button>
                </div>
                <div>
                  <img src={item.link} alt="" className=""/>
                </div>
              </div>
            )
          })
        }
      </div>
      <div>
        {
          cate.map((item, index)=>{
            return(
              <div key={index} className="flex items-center max-[980px]:flex-col">
                <div>
                  <img src={item.link} alt="" className="" />
                </div>
                <div className="basis-[90%] text-center ">
                  <h2 className="text-[40px] font-bold">{item.nome}</h2>
                  <p className="px-[20%] text-[13px]">{item.info}</p>
                  <button className="border-[2px] rounded p-1 border-[black] font-bold text-[13px] my-4 hover:scale-[1.3] transition" onClick={()=>navigate("/menu")}>Explore</button>
                </div>
              </div>
            )
          })
        }
      </div>

    </div>
  )
}

export default Dashboard